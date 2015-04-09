<?php
/*
 * Fork of Parsdown
 *
 * http://parsedown.org / (c) Emanuil Rusev (http://erusev.com)
 * 
 * For the full license information, view the LICENSE file that was distributed
 * with this source code.
 *
 * @author Jon Potiron
 * @version 2.0
 *
 */
class Parsedown{
	#
	# Philosophy
	#
	
	# Markdown is intended to be easy-to-read by humans - those of us who read
	# line by line, left to right, top to bottom. In order to take advantage of
	# this, Parsedown tries to read in a similar way. It breaks texts into
	# lines, it iterates through them and it looks at how they start and relate
	# to each other.
	
	#
	# Setters
	#
	private $breaksEnabled = false;
	private $lineBreak = "  \n";
	
	/*
	 * Enables GFM line breaks.
	 */
	function setBreaksEnabled($breaksEnabled){
		$this->breaksEnabled = $breaksEnabled;
		if($breaksEnabled){
			$this->lineBreak = "\n";
		}
	}

	#
	# Methods
	#
	function parse($text){
		# standardize line breaks
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);
		
		# replace tabs with spaces
		$text = str_replace("\t", '    ', $text);
		
		# remove surrounding line breaks
		$text = trim($text, "\n");
		
		# split text into lines
		$lines = explode("\n", $text);
		
		# iterate through lines to identify blocks
		$blocks = $this->findBlocks($lines);
		
		# iterate through blocks to build markup
		$markup = $this->compile($blocks);
		
		# trim line breaks
		$markup = trim($markup, "\n");
		
		return $markup;
	}

	#
	# Private
	#
	/*
	 * Returns block array from a string
	 *
	 * @param string $lines Lines to parse
	 * @param mixed $containerBlockName Used if function is called on a substring ("<li>" case only)
	 *
	 */
	private function findBlocks(array $lines, $containerBlockName = null){
		$block = null;
		$context = null;
		$contextData = null;
		/*
		 * Iterates through lines to identify blocks
		 */
		foreach ($lines as $line){
			$indentedLine = $line;
			$indentation = 0;
			while(isset($line[$indentation]) && $line[$indentation] === ' '){
				$indentation++;
			}
			if ($indentation > 0){
				$line = ltrim($line);
			}
			/*
			 * Stops context or sets block content
			 * depending on current context
			 * 
			 */
			switch ($context) {
				case null:
					$contextData = null;
					if ($line === ''){
						// goes to next line
						continue 2;
					}
					break;
				case 'fenced code':
					if ($line === ''){
						// sets an empty lines and continue to next
						$block['content'][0]['content'] .= "\n";
						continue 2;
					}
					if (preg_match('/^[ ]*'.$contextData['marker'].'{3,}[ ]*$/', $line)){
						// stops "fenced code" block
						$context = null;
					} else {
						// append code "as is"
						if($block['content'][0]['content']){
							$block['content'][0]['content'] .= "\n";
						}
						$string = htmlspecialchars($indentedLine, ENT_NOQUOTES, 'UTF-8');
						$block['content'][0]['content'] .= $string;
					}
					continue 2;
				case 'markup':
					/*
					 * Adds or removes nested markup depth
					 */
					if (stripos($line, $contextData['start']) !== false){
						// opening tag
						$contextData['depth'] += substr_count($line,$contextData['start']);
					}
					if (stripos($line, $contextData['end']) !== false){
						// closing tag
						$contextData['depth'] -= substr_count($line,$contextData['end']);
					}
					/*
					 * Stops "markup" context
					 */
					if($contextData['depth'] == 0){
						$context = null;
					}
					$block['content'] .= "\n".$indentedLine;
					continue 2;
				case 'list':
					if ($line === ''){
						// marks current block as temporarily interrupted and continue to next line
						$contextData['interrupted'] = true;
						continue 2;
					}
					if (
						$contextData['indentation'] === $indentation
						&& preg_match('/^'.$contextData['marker'].'[ ]+(.*)/', $line, $matches)
					){
						if (isset($contextData['interrupted'])) {
							//$nestedBlock['content'][] = '';
							unset($contextData['interrupted']);
							$context = null;
							break;
						}
						unset($nestedBlock);
						$nestedBlock = array(
							'name' => 'li',
							'content type' => 'lines',
							'content' => array(
								$matches[1]
							),
						);
						$block['content'][] = &$nestedBlock;
						continue 2;
					}
					if (!isset($contextData['interrupted'])){
						// "lazy li" line is added to quoted content
						$value = $line;
						if ($indentation > $contextData['baseline']){
							$value = str_repeat(' ', $indentation - $contextData['baseline']).$value;
						}
						$nestedBlock['content'][] = $value;
						continue 2;
					}
					if ($indentation > 0){
						$nestedBlock['content'][] = '';
						$value = $line;
						if ($indentation > $contextData['baseline']){
							$value = str_repeat(' ', $indentation - $contextData['baseline']).$value;
						}
						$nestedBlock['content'][] = $value;
						unset($contextData['interrupted']);
						continue 2;
					}
					$context = null;
					break;
				case 'quote':
					if ($line === ''){
						// marks current block as temporarily interrupted and continue to next line
						$contextData['interrupted'] = true;
						continue 2;
					}
					if (preg_match('/^>[ ]?(.*)/', $line, $matches)){
						// normal quote line is added to quoted content
						$block['content'][] = $matches[1];
						continue 2;
					}
					if (!isset($contextData['interrupted'])){
						// "lazy quote" line is added to quoted content
						$block['content'][] = $line;
						continue 2;
					}
					$context = null;
					break;
				case 'column':
					if ($line === ''){
						// marks current block as temporarily interrupted and continue to next line
						$contextData['interrupted'] = true;
						continue 2;
					}
					if (preg_match('/^\|{1,2}[ ](.*)/', $line, $matches)){
						// normal column line is added to quoted content
						$block['content'][] = $matches[1];
						continue 2;
					}
					if (empty($contextData['interrupted'])){
						// "lazy column" line is added to quoted content
						$block['content'][] = $line;
						continue 2;
					}
					$context = null;
					break;
				case 'code':
					if ($line === ''){
						// marks current block as temporarily interrupted and continue to next line
						$contextData['interrupted'] = true;
						continue 2;
					}
					if ($indentation >= 4){
						// appends content and continue to next line
						if ($contextData['interrupted']){
							$block['content'][0]['content'] .= "\n";
							unset($contextData['interrupted']);
						}
						$block['content'][0]['content'] .= "\n";
						$string = htmlspecialchars($line, ENT_NOQUOTES, 'UTF-8');
						$string = str_repeat(' ', $indentation - 4) . $string;
						$block['content'][0]['content'] .= $string;
						continue 2;
					}
					$context = null;
					break;
				case 'table':
					if ($line === ''){
						// stops "table" block and continue to next line
						$context = null;
						continue 2;
					}
					if (strpos($line, '|') !== false){
						// parse "table" line and continue to next
						$nestedBlocks = array();
						
						$substring = preg_replace('/^[|][ ]*/', '', $line);
						$substring = preg_replace('/[|]?[ ]*$/', '', $substring);
						$parts = explode('|', $substring);
						
						foreach ($parts as $index => $part){
							$substring = trim($part);
							$nestedBlock = array(
								'name' => 'td',
								'content type' => 'line',
								'content' => $substring
							);
							if (isset($contextData['alignments'][$index])){
								$nestedBlock['attributes'] = array(
									'align' => $contextData['alignments'][$index]
								);
							}
							$nestedBlocks[] = $nestedBlock;
						}
						$nestedBlock = array(
							'name' => 'tr',
							'content type' => 'blocks',
							'content' => $nestedBlocks
						);
						$block['content'][1]['content'][] = $nestedBlock;
						continue 2;
					} else {
						// exits "table" context
						$context = null;
					}
					break;
				case 'paragraph':
					if($line === ''){
						$block['name'] = 'p'; # dense li
						$context = null;
						continue 2;
					}
					if ($line[0] === '=' && chop($line, '=') === ''){
						$block['name'] = 'h1';
						$context = null;
						continue 2;
					}
					if ($line[0] === '-' && chop($line, '-') === ''){
						$block['name'] = 'h2';
						$context = null;
						continue 2;
					}
					if (strpos($line, '|') !== false && strpos($block['content'], '|') !== false && chop($line, ' -:|') === ''){
						$values = array();
						$substring = trim($line, ' |');
						$parts = explode('|', $substring);
						foreach ($parts as $part){
							$substring = trim($part);
							$value = null;
							if ($substring[0] === ':'){
								$value = 'left';
							}
							if (substr($substring, -1) === ':'){
								$value = $value === 'left' ? 'center' : 'right';
							}
							$values []= $value;
						}
						$nestedBlocks = array();
						$substring = preg_replace('/^[|][ ]*/', '', $block['content']);
						$substring = preg_replace('/[|]?[ ]*$/', '', $substring);
						$parts = explode('|', $substring);
						foreach ($parts as $index => $part){
							$substring = trim($part);
							$nestedBlock = array(
								'name' => 'th',
								'content type' => 'line',
								'content' => $substring
							);
							if (isset($values[$index])){
								$value = $values[$index];
								$nestedBlock['attributes'] = array(
									'align' => $value
								);
							}
							$nestedBlocks[] = $nestedBlock;
						}
						$block = array(
							'name' => 'table',
							'content type' => 'blocks',
							'content' => array()
						);
						$block['content'][] = array(
							'name' => 'thead',
							'content type' => 'blocks',
							'content' => array()
						);
						$block['content'][] = array(
							'name' => 'tbody',
							'content type' => 'blocks',
							'content' => array()
						);
						$block['content'][0]['content'][] = array(
							'name' => 'tr',
							'content type' => 'blocks',
							'content' => array()
						);
						$block['content'][0]['content'][0]['content'] = $nestedBlocks;
						$context = 'table';
						$contextData = array(
							'alignments' => $values
						);
						continue 2;
					}
					break;
				default:
					throw new Exception('Unrecognized context - '.$context);
			}
			/*
			 * Sets new block (and context) depending on markup found on line start
			 */
			if($indentation >= 4){
				/*
				 * sets a new <pre> block containing a <code> block and continue to next line
				 * context is set to "code"
				 */
				$blocks[] = $block;
				$string = htmlspecialchars($line, ENT_NOQUOTES, 'UTF-8');
				$string = str_repeat(' ', $indentation - 4).$string;
				$block = array(
					'name' => 'pre',
					'content type' => 'blocks',
					'content' => array(
						array(
							'name' => 'code',
							'content type' => null,
							'content' => $string,
							'attributes' => array(
								'class' => 'language-none'
							)
						)
					),
					'attributes' => array(
						'class' => 'code line-numbers language-none'
					)
				);
				$context = 'code';
				continue;
			}
			switch($line[0]){
				case '-':
				case '=':
				case '#':
					if (isset($line[1])){
						$tag_char = $line[0];
						if($tag_char == '-' && !preg_match('/^\-{2,}/',$line)){
							// hyphen is used for ul
							continue;
						} elseif($line[1] == '>'){
							// hyphen is used for => or ->
							continue;
						} else {
							/*
							 * sets a new <h[x]> block and continue to next line
							 * context is set to NULL
							 */
							$blocks[] = $block;
							$level = 1;
							while (isset($line[$level]) && $line[$level] === $tag_char){
							    $level++;
							}
							if(in_array($tag_char,array('-','='))){
								// inverted level
								$level = 7 - $level;
							}
							$level = $level > 6 ? 6 : ($level < 1 ? 1 : $level);
							$string = trim($line, $tag_char.' ');
							$string = $this->parseLine($string);
							$block = array(
							    'name' => 'h'.$level,
							    'content type' => 'line',
							    'content' => $string,
							    'attributes' => ($tag_char == '-' ? array('class'=>'noborder') : array())
							);
							$context = null;
							continue 2;
						}
					}
					break;
				case '<':
					$closing_bracket_position = strpos($line, '>');
					if ($closing_bracket_position > 1){
						// finds tag name
						$substring = substr($line, 1, $closing_bracket_position - 1);
						$substring = chop($substring);
						if (substr($substring, -1) === '/'){
							// auto closing tag
							$isClosing = true;
							$substring = substr($substring, 0, -1);
						}
						$first_space_position = strpos($substring, ' ');
						if ($first_space_position !== false){
							$name = substr($substring, 0, $first_space_position);
						} else {
							$name = $substring;
						}
						$name = strtolower($name);
						/*
						 * Checks if tag_name is alpha (no need since we don't check "real" existence of tag name)
						 */
						//if ($name[0] == 'h' and strpos('123456', $name[1]) !== false){
						//	#  hr, h1, h2, ...
						//}elseif ( ! ctype_alpha($name)){
						//	break;
						//}
						
						/*
						 * Checks if is a self closing element
						 */
						if(in_array($name,array('br','hr','img'))){
							$isClosing = true;
						}
						
						/*
						 * Checks so called $textLevelElements for no specific reason ...
						 */
						//if (in_array($name, self::$textLevelElements)){
						//	break;
						//}
						
						/*
						 * sets a new <NULL> block
						 * context is not touched
						 */
						$blocks[] = $block;
						$block = array(
							'name' => null,
							'content type' => null,
							'content' => $indentedLine
						);
						if(isset($isClosing) && $isClosing){
							/*
							 * continue to next line
							 */
							unset($isClosing);
							continue 2;
						} else {
							/*
							 * continue to next line
							 * context is set to "markup"
							 */
							$context = 'markup';
							$contextData = array(
								'start' => '<'.$name,
								'end' => '</'.$name.'>',
								'depth' => 0
							);
							$openning_tag_count = substr_count($line,$contextData['start']);
							$closing_tag_count = substr_count($line,$contextData['end']);
							$contextData['depth'] = $openning_tag_count - $closing_tag_count;
							if(
								stripos($line, $contextData['end']) !== false
								&& $openning_tag_count == $closing_tag_count
							){
								$context = null;
							}
							continue 2;
						}
					}
					break;
				case '>':
					if (preg_match('/^>[ ]?(.*)/', $line, $matches)){
						/*
						 * sets a new <blockquote> block and continue to next line
						 * context is set to "quote"
						 */
						$blocks[] = $block;
						$block = array(
							'name' => 'blockquote',
							'content type' => 'lines',
							'content' => array(
								$matches[1]
							)
						);
						$context = 'quote';
						$contextData = array();
						continue 2;
					}
					break;
				case '|':
					if (preg_match('/^(\|{1})[ ](.*)/', $line, $matches)){
						/*
						 * sets a new <div class="column"> block and continue to next line
						 * context is set to "column"
						 */
						$blocks[] = $block;
						$block = array(
							'name' => 'div',
							'content type' => 'lines',
							'content' => array(
								$matches[2]
							),
							'attributes' => array(
								'class' => 'column'
							)
						);
						$context = 'column';
						$contextData = array();
						continue 2;
					}
					break;
				case '[':
					$position = strpos($line, ']:');
					if ($position){
						/*
						 * add a new link in $this->referenceMap
						 * 
						 */
						$reference = array();
						$label = substr($line, 1, $position - 1);
						$label = strtolower($label);
						$substring = substr($line, $position + 2);
						$substring = trim($substring);
						if ($substring === ''){
							break;
						}
						if ($substring[0] === '<'){
							$position = strpos($substring, '>');
							if ($position === false){
								break;
							}
							$reference['link'] = substr($substring, 1, $position - 1);
							$substring = substr($substring, $position + 1);
						} else {
							$position = strpos($substring, ' ');
							if ($position === false){
								$reference['link'] = $substring;
								$substring = false;
							} else {
								$reference['link'] = substr($substring, 0, $position);
								$substring = substr($substring, $position + 1);
							}
						}
						if ($substring !== false){
							if ($substring[0] !== '"' && $substring[0] !== "'" && $substring[0] !== '('){
								break;
							}
							$lastChar = substr($substring, -1);
							if ($lastChar !== '"' && $lastChar !== "'" && $lastChar !== ')'){
								break;
							}
							$reference['title'] = substr($substring, 1, -1);
						}
						$this->referenceMap[$label] = $reference;
						continue 2;
					}
					break;
				case '`':
				case '~':
					if (preg_match('/^([`]{3,}|[~]{3,})[ ]*(\w+)?[ ]*$/', $line, $matches)){
						/*
						 * sets a new <pre> block containing a <code> block and continue to next line
						 * context is set to "fenced code"
						 */
						$blocks[] = $block;
						$block = array(
							'name' => 'pre',
							'content type' => 'blocks',
							'content' => array(
								array(
									'name' => 'code',
									'content type' => null,
									'content' => ''
								)
							),
							'attributes' => array(
								'class' => 'code line-numbers language-'.(isset($matches[2]) ? $matches[2] : 'none')
							)
						);
						$block['content'][0]['attributes'] = array(
							'class' => 'language-'.(isset($matches[2]) ? $matches[2] : 'none'),
						);
						$context = 'fenced code';
						$contextData = array(
							'marker' => $matches[1][0],
						);
						continue 2;
					}
					break;
				case '-':
				case '*':
				case '_':
					if (preg_match('/^([-*_])([ ]{0,2}\1){2,}[ ]*$/', $line)){
						/*
						 * sets a new <hr> block continue to next line
						 * context is set to NULL
						 */
						$blocks []= $block;
						$block = array(
							'name' => 'hr',
							'content' => null
						);
						$context = null;
						continue 2;
					}
					break;
			}
			/*
			 * Creates a new <ol> or <ul> block and continue to next line
			 */
			if(
				($line[0] <= '-' && preg_match('/^([*+-][ ]+)(.*)/', $line, $matches)) ||
				($line[0] <= '9' && preg_match('/^([0-9]+[.][ ]+)(.*)/', $line, $matches))
			){
				$blocks[] = $block;
				$name = $line[0] >= '0' ? 'ol' : 'ul';
				$block = array(
					'name' => $name,
					'content type' => 'blocks',
					'content' => array()
				);
				unset($nestedBlock);
				$nestedBlock = array(
					'name' => 'li',
					'content type' => 'lines',
					'content' => array(
						$matches[2]
					)
				);
				$block['content'][] = &$nestedBlock;
				$baseline = $indentation + strlen($matches[1]);
				$marker = $line[0] >= '0' ? '[0-9]+[.]' : '[*+-]';
				$context = 'list';
				$contextData = array(
					'indentation' => $indentation,
					'baseline' => $baseline,
					'marker' => $marker,
					'lines' => array(
						$matches[2]
					)
				);
				continue;
			}
			/*
			 * Appends to current <p> block and continue to next line
			 * or Creates a new <p> block and continue to next line
			 */
			if ($context === 'paragraph'){
				$block['content'] .= "\n".$line;
				continue;
			} else {
				$blocks[] = $block;
				$block = array(
					'name' => 'p',
					'content type' => 'line',
					'content' => $line
				);
				/*
				 * Method has been called on a <li> substring => no need to create a new <p>
				 */
				if ($containerBlockName === 'li' && !isset($blocks[1])){
					$block['name'] = null;
				}
				$context = 'paragraph';
			}
		}
		/*
		 * Method has been called on a <li> substring
		 */
		if ($containerBlockName === 'li' && (is_null($block) || is_null($block['name']))){
			return is_null($block) ? '' : $block['content'];
		}
		/*
		 * Adds last block, removes first empty block and returns
		 */
		$blocks[] = $block;
		unset($blocks[0]);
		return $blocks;
	}

	/*
	 * Returns parsed markup string from a block list
	 *
	 * @param array $blocks
	 * @return string
	 */
	private function compile(array $blocks) {
		$markup = '';
		foreach ($blocks as $block){
			/*
			 * Sets beginning markup tag if needed
			 * Breaks to next block if tag is self closed
			 */
			if (isset($block['name'])){
				$markup .= "\n";
				$markup .= '<'.$block['name'];
				if (isset($block['attributes'])) {
					foreach ($block['attributes'] as $name => $value){
						$markup .= ' '.$name.'="'.$value.'"';
					}
				}
				if ($block['content'] === null){
					$markup .= ' />';
					continue;
				} else {
					$markup .= '>';
				}
			}
			/*
			 * Parses different block types
			 */
			if(array_key_exists('content type',$block)){
				switch ($block['content type']){
					case null:
						/*
						 * <code> or "markup" doesn't need further parsing
						 */
						$markup .= $block['content'];
						break;
					case 'line':
						/*
						 * <td>, <th>, <h[x]>, <p> needs line parsing
						 */
						$markup .= $this->parseLine($block['content']);
						break;
					case 'lines':
						/*
						 * <li>, <blockquote>, <div class="column"> needs content to be parsed for blocks
						 *
						 */
						$result = $this->findBlocks($block['content'], $block['name']);
						if (is_string($result)){
							# dense li
							$markup .= $this->parseLine($result);
						} else {
							$markup .= $this->compile($result);
						}
						break;
					case 'blocks':
						/*
						 * <table>,<thead>,<tbody>,<pre>,<ol>,<ul> needs to be parsed for sub blocks
						 */
						$markup .= $this->compile($block['content']);
						break;
				}
			}
			/*
			 * Sets ending markup tag if needed
			 */
			if (isset($block['name'])){
			    $markup .= '</'.$block['name'].'>';
			}
		}
		$markup .= "\n";

		return $markup;
	}
	
	/*
	 * Parses special html chars ( ®, ©, ™, ♥, →, ←)
	 */
	private function parseSpecialChars($text){
		// replace special chars
		$s = array('(R)','(C)','(TM)','<3','=>','->','<=','<-');
		$r = array('&reg;','&copy;','&trade;','&hearts;','&rarr;','&rarr;','&larr;','&larr;');
		return str_replace($s,$r,$text);
	}
	
	/*
	 * Adds nonbreaking space to numerical expressions
	 * (to avoid "39 + 3" or "4 horsemen" to be broke on multiple lines)
	 */
	private function parseNumericalExpression($text){
		$text = preg_replace('/(\d) ([a-zA-Z_\-\+\/\*]+) (\d)/','$1&nbsp;$2&nbsp;$3',$text);
		$text = preg_replace('/(\d) ([a-zA-Z])/','$1&nbsp;$2',$text);
		return $text;
	}
	
	/*
	 * Parses inline elements
	 */
	private function parseLine($text, $markers = array("  \n", '![', '&', '*', '<', '[#]', '[', '\\', '_', '`', 'http', '~~')){
		/*
		 * Clean strict Markdown line breaks.
		 */
		if($this->breaksEnabled){
			$break_key = array_search("  \n",$markers);
			if($break_key !== false){
				$markers[$break_key] = $this->lineBreak;
			}
			$text = str_replace("  \n",$this->lineBreak,$text);
		}
		/*
		 * Nothing to parse
		 */
		if (!isset($text[1]) || $markers === array()){
			return $text;
		}
		/*
		 * Parses inline elements
		 */
		$text = $this->parseSpecialChars($text);
		$text = $this->parseNumericalExpression($text);
		$markup = '';
		while ($markers){
			$closestMarker = null;
			$closestMarkerIndex = 0;
			$closestMarkerPosition = null;
			/*
			 * Finds closest marker in line
			 */
			foreach ($markers as $index => $marker){
				$markerPosition = strpos($text, $marker);
				if ($markerPosition === false){
					unset($markers[$index]);
					continue;
				}
				if ($closestMarker === null || $markerPosition < $closestMarkerPosition){
					$closestMarker = $marker;
					$closestMarkerIndex = $index;
					$closestMarkerPosition = $markerPosition;
				}
			}
			/*
			 * Skips text not needing parsing
			 */
			if ($closestMarker === null || isset($text[$closestMarkerPosition + 1]) === false){
			    $markup .= $text;
			    break;
			} else {
			    $markup .= substr($text, 0, $closestMarkerPosition);
			}
			/*
			 * Gets remaining text to be parsed behind marker
			 */
			$text = substr($text, $closestMarkerPosition);
			/*
			 * Parses marker content and finds char offset to begin next parsing
			 */
			unset($markers[$closestMarkerIndex]);
			switch ($closestMarker){
				case $this->lineBreak:
				//case "  \n":
					/*
					 * Adds a newline
					 */
					$markup .= '<br>'."\n";
					$offset = strlen($this->lineBreak);
					break;
				case '[#]':
					/*
					 * Adds an anchor / bookmark link inside the document
					 * (i.e. item tracked by href="#[item_name]")
					 */
					if(preg_match('/^\[#\]([a-zA-Z0-9_-]+)/',$text, $matches)){
						$markup .= '<span name="'.$matches[1].'" id="'.$matches[1].'">'.$matches[1].'</span>';
						$offset = strlen($matches[0]);
					} else {
						$markup .= $closestMarker;
						$offset = 3;
					}
					break;
				case '![':
				case '[':
					/*
					 * Adds an image or a link
					 */
					if (strpos($text, ']') && preg_match('/\[((?:[^][]|(?R))*)\]/', $text, $matches)) {
						/*
						 * Checks if image or link
						 */
						$element = array(
							'!' => $text[0] === '!',
							'text' => $matches[1],
						);
						$offset = strlen($matches[0]);
						if ($element['!']) {
						    $offset++;
						}
						/*
						 * Parse link
						 */
						$remainingText = substr($text, $offset);
						if ($remainingText[0] === '(' && preg_match('/\([ ]*(.*?)(?:[ ]+[\'"](.+?)[\'"])?[ ]*\)/', $remainingText, $matches)){
							$element['link'] = $matches[1];
							if (isset($matches[2])) {
								$element['title'] = $matches[2];
							}
							$offset += strlen($matches[0]);
						} elseif ($this->referenceMap) {
							$reference = $element['text'];
							if (preg_match('/^\s*\[(.*?)\]/', $remainingText, $matches)) {
								$reference = $matches[1] === '' ? $element['text'] : $matches[1];
								$offset += strlen($matches[0]);
							}
							$reference = strtolower($reference);
							if (isset($this->referenceMap[$reference])) {
								$element['link'] = $this->referenceMap[$reference]['link'];
								if (isset($this->referenceMap[$reference]['title'])) {
								    $element['title'] = $this->referenceMap[$reference]['title'];
								}
							} else {
								unset($element);
							}
						} else {
							unset($element);
						}
					}
					if (isset($element)){
						/*
						 * No link has been found => must be an image
						 */
						$element['link'] = str_replace('&', '&amp;', $element['link']);
						$element['link'] = str_replace('<', '&lt;', $element['link']);
						if ($element['!']){
							$markup .= '<img alt="'.$element['text'].'" src="'.$element['link'].'"';
							if (isset($element['title'])){
								$markup .= ' title="'.$element['title'].'"';
							}
							$markup .= ' />';
						} else {
							$element['text'] = $this->parseLine($element['text'], $markers);
							$markup .= '<a href="'.$element['link'].'"';
							if (isset($element['title'])){
								$markup .= ' title="'.$element['title'].'"';
							}
							$markup .= '>'.$element['text'].'</a>';
						}
						unset($element);
					} else {
						$markup .= $closestMarker;
						$offset = $closestMarker === '![' ? 2 : 1;
					}
					break;
				case '&':
					/*
					 * Adds an html entity
					 * or translates "&" to "&amp;"
					 */
					if (preg_match('/^&#?\w+;/', $text, $matches)){
						$markup .= $matches[0];
						$offset = strlen($matches[0]);
					} else {
						$markup .= '&amp;';
						$offset = 1;
					}
					break;
				case '*':
				case '_':
					/*
					 * Adds emphasis (nested)
					 */
					if ($text[1] === $closestMarker && preg_match(self::$strongRegex[$closestMarker], $text, $matches)){
						/*
						 * Found <strong> marker
						 */
						$markers[$closestMarkerIndex] = $closestMarker;
						$matches[1] = $this->parseLine($matches[1], $markers);
						$markup .= '<strong>'.$matches[1].'</strong>';
					} elseif(preg_match(self::$emRegex[$closestMarker], $text, $matches)){
						/*
						 * Found <em> marker
						 */
						$markers[$closestMarkerIndex] = $closestMarker;
						$matches[1] = $this->parseLine($matches[1], $markers);
						$markup .= '<em>'.$matches[1].'</em>';
					}
					if (isset($matches) && $matches !== false && !empty($matches)){
						$offset = strlen($matches[0]);
					} else {
						$markup .= $closestMarker;
						$offset = 1;
					}
					break;
				case '<':
					/*
					 * Sest parsedown "Automatic Link" (<http://www.example.com>)
					 * or translates "<" to "&lt;"
					 */
					if (strpos($text, '>') !== false){
						if ($text[1] === 'h' && preg_match('/^<(https?:[\/]{2}[^\s]+?)>/i', $text, $matches)){
							/*
							 * Classic "a href"
							 */
							$elementUrl = $matches[1];
							$elementUrl = str_replace('&', '&amp;', $elementUrl);
							$elementUrl = str_replace('<', '&lt;', $elementUrl);
							$markup .= '<a href="'.$elementUrl.'">'.$elementUrl.'</a>';
							$offset = strlen($matches[0]);
						} elseif (strpos($text, '@') > 1 and preg_match('/<(\S+?@\S+?)>/', $text, $matches)) {
							/*
							 * Mailto "a"
							 */
							$markup .= '<a href="mailto:'.$matches[1].'">'.$matches[1].'</a>';
							$offset = strlen($matches[0]);
						} elseif (preg_match('/^<\/?\w.*>/', $text, $matches)) {
							/*
							 * Markup tag is ignored
							 */
							$markup .= $matches[0];
							$offset = strlen($matches[0]);
						} else {
							$markup .= '&lt;';
							$offset = 1;
						}
					} else {
						$markup .= '&lt;';
						$offset = 1;
					}
					break;
				case '\\':
					/*
					 * Escaped chars
					 */
					if (in_array($text[1], self::$specialCharacters)){
						$markup .= $text[1];
						$offset = 2;
					} else {
						$markup .= '\\';
						$offset = 1;
					}
					break;
				case '`':
					/*
					 * Adds inline code
					 */
					if (preg_match('/^(`+)[ ]*(.+?)[ ]*(?<!`)\1(?!`)/', $text, $matches)){
						$elementText = $matches[2];
						$elementText = htmlspecialchars($elementText, ENT_NOQUOTES, 'UTF-8');
						$markup .= '<code>'.$elementText.'</code>';
						$offset = strlen($matches[0]);
					} else {
						$markup .= '`';
						$offset = 1;
					}
					break;
				case 'http':
					/*
					 * Automatically adds links
					 */
					if (preg_match('/^https?:[\/]{2}[^\s]+\b\/*/ui', $text, $matches)){
						$elementUrl = $matches[0];
						$elementUrl = str_replace('&', '&amp;', $elementUrl);
						$elementUrl = str_replace('<', '&lt;', $elementUrl);
						$markup .= '<a href="'.$elementUrl.'">'.$elementUrl.'</a>';
						$offset = strlen($matches[0]);
					} else {
						$markup .= 'http';
						$offset = 4;
					}
					break;
				case '~~':
					/*
					 * Adds strikethrough
					 */
					if (preg_match('/^~~(?=\S)(.+?)(?<=\S)~~/', $text, $matches)){
						$matches[1] = $this->parseLine($matches[1], $markers);
						$markup .= '<del>'.$matches[1].'</del>';
						$offset = strlen($matches[0]);
					} else {
						$markup .= '~~';
						$offset = 2;
					}
					break;
			}
			/*
			 * Clears parsed part from text
			 */
			if (isset($offset)) {
				$text = substr($text, $offset);
			}
			$markers[$closestMarkerIndex] = $closestMarker;
		}

		return $markup;
	}

	#
	# Static
	#
	static function instance($name = 'default'){
		if (isset(self::$instances[$name])){
			return self::$instances[$name];
		}
		$instance = new Parsedown();
		self::$instances[$name] = $instance;
		return $instance;
	}

	private static $instances = array();

	#
	# Fields
	#
	private $referenceMap = array();

	#
	# Read-only
	#
	/*
	 * Sets regular expresisions for <em> and <strong> detection
	 */
	private static $strongRegex = array(
		'*' => '/^[*]{2}((?:[^*]|[*][^*]*[*])+?)[*]{2}(?![*])/s',
		'_' => '/^__((?:[^_]|_[^_]*_)+?)__(?!_)/us',
	);
	private static $emRegex = array(
		'*' => '/^[*]((?:[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
		'_' => '/^_((?:[^_]|__[^_]*__)+?)_(?!_)\b/us',
	);
	
	/*
	 * Sets special chars that can be escaped
	 */
	private static $specialCharacters = array(
		'\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '>', '|', '#', '+', '-', '.', '!',
	);

	//private static $textLevelElements = array(
	//	'a', 'br', 'bdo', 'abbr', 'blink', 'nextid', 'acronym', 'basefont',
	//	'b', 'em', 'big', 'cite', 'small', 'spacer', 'listing',
	//	'i', 'rp', 'sub', 'code',          'strike', 'marquee',
	//	'q', 'rt', 'sup', 'font',          'strong',
	//	's', 'tt', 'var', 'mark',
	//	'u', 'xm', 'wbr', 'nobr',
	//				   'ruby',
	//				   'span',
	//				   'time',
	//);
}
