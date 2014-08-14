<?php
//session_start();
include 'utilities.php';
//connect_to_id_store();
//verify_login();

?>
<html>

<?php create_editor(); ?>

</head>
<body class="easyui-layout" id="cc">
<form id="ff">
<input type="hidden" name="lang"/>
<input type="hidden" name="code"/>
<input type="hidden" name="inputs"/>
<div id="loading"></div>
<div data-options="region:'north',split:false,border:false" style="height:70px; repeat-x !important;"><!-- TOP MENU STARTS -->
<div>
    <?php create_nav_bar(); ?>
</div>
</div> 
<!-- TOP MENU ENDS -->
   <div data-options="region:'south',split:false,border:false" style="height:60px;">
   <!-- BOTTOM STARTS -->
      <table style="width:99.6%; margin-top:5px;">
      <tr>
      <td style="padding-left:10px;font:16px bold;text-align:right;white-space: nowrap;">Command Line Arguments: </td>
      <td style="margin-top:5px;text-align:left;white-space: nowrap;"><a title="What is this?" href="/command_line_arguments.php" target="contentview" class="easyui-linkbutton" data-options="iconCls:'icon-help',plain:true"></a></td>
      <td style="width:46%;padding-top:4px;"><input class="easyui-validatebox" type="text" data-options="required:false" style="width:100%;height:25px;padding-left:5px; border:1px solid #bad5ff;overflow:auto;resize:none;" name="args"/>   </td>
      <td style="padding-left:10px;font:16px bold;text-align:right;white-space: nowrap;">STDIN Input: </td>   <td style="margin-top:5px;text-align:left;white-space: nowrap;"><a title="What is this?" href="/stdin_input.php" target="contentview" class="easyui-linkbutton" data-options="iconCls:'icon-help',plain:true"></a></td>
      <td style="width:54%;padding-top:4px;"><input class="easyui-validatebox" type="text" data-options="required:false" style="width:100%;height:25px;padding-left:5px; border:1px solid #bad5ff;overflow:auto;resize:none;" name="stdinput"/>   </tr>
      </table></div>
  <!-- BOTTOM ENDS -->
<div data-options="region:'east',iconCls:'icon-result',title:'Result',split:true,tools:'#tab-tools2',toolPosition:'right'" id="right" style="width:480px;"><!-- RIGHT PANEL STARTS --> 
<iframe id="view" name="contentview" src="" style="background:#fff;position:relative;width:100%;height:99%;border:0px solid #aaa;margin:0px;padding:0px;overflow:auto;">
</iframe><div id="tab-tools2">
<a href="/download.php" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-download'" target="contentview" style="position:relative;top:-5px;width:120px;white-space:nowrap;"><b>Download Files</b></a>
</div>
</div><!-- RIGHT PANEL ENDS --> 
  
<div data-options="region:'center'" style="height:90px; padding:0px;" id="left"><!-- LEFT STARTS -->
<div id='check' style='position:absolute;z-index:1001;right:10px; top:4px;'>
  <select id="editor" onchange="setEditor();">
    <option value="default">Default Ace Editor</option>
    <option>The vim Editor</option>
    <option>The emacs Editor</option>
  </select>
</div>
<div data-options="fit:true,border:false,tools:'#tab-tools',toolPosition:'left'" id="tt" class="easyui-tabs">
<div title="wordcount.R" style="padding:0px;">
<pre id="code" class="editclass">

library(SparkR)

args <- commandArgs(trailing = TRUE)

if (length(args) != 2) {
  print("Usage: wordcount <master> <file>")
            q("no")
            }

            # Initialize Spark context
            sc <- sparkR.init(args[[1]], "RwordCount")
            lines <- textFile(sc, args[[2]])

            words <- flatMap(lines,
            function(line) {
            strsplit(line, " ")[[1]]
            })
            wordCount <- lapply(words, function(word) { list(word, 1L) })

            counts <- reduceByKey(wordCount, "+", 2L)
            output <- collect(counts)

            for (wordcount in output) {
            cat(wordcount[[1]], ": ", wordcount[[2]], "\n")
</pre>
</div> 
<div title="input.txt" style="overflow:auto;padding:20px;">
<pre id="inputs" class="editclass">This is the file you can use to provide input.
</pre> 
</div>
</div>
<div id="tab-tools" style="border-top:0px; border-right:0px;">
      <div id='wait' style='display:none'>
          <img style="margin-left:4px;margin-top:3px;width:28px; height:28px;" src='/images/loading.gif'/>
      </div>
       <a href="javascript:void(0)" onclick="javascript:submitForm();return false" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-compile'" target="view" style="width:150px;white-space: nowrap;"><b>Execute Script</b></a></div>

  
</div><!-- LEFT ENDS -->
</form>
<script>
   var editors = {};
   editors['code'] = new ace.edit('code');
   editors['inputs'] = new ace.edit('inputs');

   editors['code'].setTheme("ace/theme/crimson_editor");
   editors['inputs'].setTheme("ace/theme/crimson_editor");

   editors['code'].getSession().setMode("ace/mode/r");

   editors['code'].getSession().setUseWrapMode(true);
   editors['inputs'].getSession().setUseWrapMode(true);

   editors['code'].getSession().setTabSize(2);
   editors['inputs'].getSession().setTabSize(2);

   $("#code").resize(function() {
        editors['code'].resize(true);
   });
   $("#inputs").resize(function() {
        editors['inputs'].resize(true);
   });
   
   $( "#tt" ).tabs({
      onSelect: function( title, index ) {
         if( index == 0 ){
            editors['code'].resize(true);
            editors['code'].focus();
         }else if( index == 1 ){
            editors['inputs'].resize(true);
            editors['inputs'].focus();
         }
      }
   });
   $("#version").text(" - Execute R Script");
   function submitForm(){
      $("[name='lang']").val("rscript");
      $("[name='code']").val(editors['code'].getValue());
      $("[name='inputs']").val(editors['inputs'].getValue());
      $('#wait').show(); 
      var url = "execute_new.php";
      $.ajax({
        type: "POST",
        url: url,
        target: "view",
        data: $("#ff").serialize(),
        success: function(data)
        {
            $('#view').contents().find("html").html(data);
            $('#wait').hide();
            return false;
        }
      });
      return false; // avoid to execute the actual submit of the form.
   }    
</script>
</div>
</div>
</body>
</html>
