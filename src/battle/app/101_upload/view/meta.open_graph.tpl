{************************************************

 Open Graph Meta Properties
 
 in :
 - $dat_file : DatFile

************************************************}
<meta name="description" content="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}.">
<!-- OG -->
<meta property="og:site_name" content="NanoChan">
<meta property="og:title" content="{$dat_file->date_modified|date_format:'%d/%m/%Y %T'} - NanoChan" />
<meta property="og:url" content="{$batl_full_url}{$batl_current_app_virtual_url}" />
<meta property="og:image" content="{$dat_file->absolute_url}" />
<meta property="og:description" content="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}." />
<!-- TW -->
<meta name="twitter:site" content="@NanoChan">
<meta name="twitter:domain" value="labelcarrote.com" />
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" value="{$dat_file->date_modified|date_format:'%d/%m/%Y %T'} - NanoChan" />
<meta name="twitter:description" value="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}." />
<meta name="twitter:image" content="{$dat_file->absolute_url}" />
<meta name="twitter:url" value="{$batl_full_url}{$batl_current_app_virtual_url}" />

{**
<!-— facebook open graph tags -->
<meta property="og:site_name" content="NanoChan">
<meta property="og:title" content="{$dat_file->date_modified|date_format:'%d/%m/%Y %T'} - NanoChan" />
<meta property="og:url" content="{$batl_current_app_virtual_url}" />
<meta property="og:image" content="{$dat_file->absolute_url}" />
<meta property="og:type" content="website" />
<meta property="og:description" content="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}." />
<meta property="og:image:type" content="image/{$dat_file->extension}" />
<meta property="og:image:width" content="400" />
<meta property="og:image:height" content="300" />
 
<!-— twitter card tags additive with the og: tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:domain" value="labelcarrote.com" />
<meta name="twitter:title" value="NanoChan" />
<meta name="twitter:description" value="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}." />
<meta name="twitter:image" content="{$dat_file->absolute_url}" />
<meta name="twitter:url" value="{$batl_current_app_virtual_url}" />
<meta name="twitter:label1" value="Last Image :" />
<meta name="twitter:data1" value="{$dat_file->date_modified|date_format:'%d/%m/%Y %T'}" />
<meta name="twitter:label2" value="Or on demand" />
<meta name="twitter:data2" value="at Hulu.com" />
**}