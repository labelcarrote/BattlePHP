<div class="content line">
    <section class="unit size1of1">
        <form id="card_edit_form" class="form-inline" method="POST" enctype="multipart/form-data" action="{$current_app_virtual_url}">
            <input type="hidden" name="name" value="{$card->name}"/>
            <fieldset>
                <legend class="borderbottomorange">
                    <a href="{$current_app_virtual_url}{$card->name}"><h1>Update {$card->name} !</h1></a>
                </legend>

                <!--  Color for headers, links and horizontal bar  -->
                <span class="help-inline">Color : </span>
                <input type="text"class="input-large" name="color" placeholder="ex: #FF9900" value="{$card->color}">
                
                <!-- Private Card ? -->
                <label class="checkbox">
                    <input type="checkbox" name="is_private" {if $card->is_private}checked{/if}> Is Private ?
                </label>

                <!-- TEXT -->
                <div id="editor_container" class="margintop">
                    <pre id="editor">{$card->text_code}</pre>
                </div>
                <!-- 
                <textarea class="hidden" type="text" name="card"></textarea>
                 -->
            </fieldset>
        </form>

        <!-- SUBMIT -->
        <div class="margintopbottom">
            <button id="editor_save" class="btn btn-primary">Save</button>
            <!-- <input class="btn btn-primary" type="submit" name="submit" value="save"/> -->
            <a class="btn btn-secondary" href="{$current_app_virtual_url}{$card->name}">Cancel</a>
        </div>

        <!-- FILES -->
        <div class="border padding darker marginbottom">
            <div class="line">
                <div class="unit line size1of5 margintopbottom">
                    <form id="addfileform" class="">
                        <input type="hidden" name="name" value="{$card->name}">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <span class="btn btn-file">
                                <span class="fileupload-new">Attach / Upload File</span>
                                <span class="fileupload-exists">Attach / Upload File</span>
                                <input name="file" id="file" type="file">
                            </span>
                        </div>
                    </form>   
                </div>
                <div class="unit size4of5">
                    <div class="uploadprogress margintopbottom">
                        <div class="bar"></div>
                        <p>Progress</p>
                    </div>
                </div>
            </div>
            <div id="files">
                <ul>
                    {foreach from=$card->files item=file}
                    <li>@{$file->name} (<a style="color:{$card->color}" href="{$root_url}{$file->fullname}" alt="{$file->name}">see</a>) size : {$file->size}</li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </section>
</div>
<script src="{$root_url}lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>        
<script type="text/javascript" src="{$current_app_url}public/js/sawhat.js"></script>
