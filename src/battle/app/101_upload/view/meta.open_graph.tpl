{************************************************

 Open Graph Meta Properties
 
 in :
 - $dat_file : DatFile

************************************************}
<meta name="description" content="Click on the picture to replace it with any .jpg, .png or .gif file &lt; {$upload_form->max_file_size_human_readable}.">
<!-- OG -->
<meta property="og:site_name" content="NanoChan">
<meta property="og:title" content="{$dat_file->date_modified|date_format:'%d/%m/%Y %T'} - NanoChan" />
<meta property="og:url" content="{$batl_current_app_virtual_url}" />
<meta property="og:image" content="{$dat_file->absolute_url}" />
<!-- TW -->
<meta name="twitter:site" content="@NanoChan">
{**
<meta property="og:type" content="website" />
<meta property="og:image:type" content="image/{$dat_file->extension}" />
<meta property="og:image:width" content="400" />
<meta property="og:image:height" content="300" />
**}