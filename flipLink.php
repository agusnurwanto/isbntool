<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flip Link ISBN Tools</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
    <link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
 
  <div class="container">
    <h1>Application CRUD Flip ISBN Tools</h1>
 
    <br>
    <h2>ISBNs Data</h2>
    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>ISBN</th>
          <th>Site</th>
          <th>Link</th>
          <th>Bookbyte Link</th>
          <th>Price</th>
          <th>Bookbyte Price</th>
          <th style="width:125px;">Action</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
 
      <tfoot>
        <tr>
          <th>ID</th>
          <th>ISBN</th>
          <th>Site</th>
          <th>Link</th>
          <th>Bookbyte Link</th>
          <th>Price</th>
          <th>Bookbyte Price</th>
          <th style="width:125px;">Action</th>
        </tr>
      </tfoot>
    </table>
     <div class="row">
      <div class="col-md-12">
        <h2>How to use:</h2>  
        <ol>
          <li>Install Chrome extension</li>
        </ol>
      </div>
    </div>

    <br>
    <div class="row">
      <div class="col-md-12">
        <h2>Changelog</h2>  
        <ul>
          <li>
            Update 22 Dec 2015 :
            <ul>
              <li>Setting environtment.</li>
            </ul>
          </li>
        </ul>
      </div>
  </div>
 
  <script src="js/jquery.min.js"></script>
  <script src="plupload/js/plupload.full.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.tableTools.js"></script>
  <script src="js/dataTables.bootstrap.min.js"></script>
  <script src="js/sweetalert.min.js"></script>
 
  <script type="text/javascript">
 
    var save_method; //for save method string
    var table;
    $(document).ready(function() {
      table = $('#table').DataTable({
        "aLengthMenu": [[100, 10, 20, 50, -1], [100, 10, 20, 50, "All"]],
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "ajaxFlipLink.php?list=true",
            "type": "POST",
            dataSrc: function(d){
              $("#lastID").val(d.lastID);
              window.data = d.data;
              for(var j in d.data){
                for(var i=0; i<10; i++){
                  if(!d.data[j][1][i]){
                    d.data[j][1][i] = "X";
                  }
                }
              }
              return d.data;
            }
        },
        "order": [[ 0, "desc" ]],
 
        //Set column definition initialisation properties.
        "columnDefs": [
          {
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
          }
        ],
        "columns": [
          { "width": "5%" },
          { "width": "10%" },
          { "width": "10%" },
          { "width": "20%" },
          { "width": "20%" },
          { "width": "20%" },
          { "width": "15%" }
        ],
        dom: 'T<"clear">lfrtip',
        select: true,
        // http://www.datatables.net/release-datatables/extensions/TableTools/examples/swf_path.html
        oTableTools: {
            "sSwfPath": "swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "copy",
                    "fnComplete": function (nButton, oConfig, oFlash) {
                        table.columns( [0, 5] ).visible( false );
                        $(window).keyup(closePrintView);
                    }
                },
                {
                    "sExtends": "csv",
                    "fnComplete": function (nButton, oConfig, oFlash) {
                        table.columns( [0, 5] ).visible( false );
                        $(window).keyup(closePrintView);
                    }
                },
                {
                    "sExtends": "xls",
                    "fnComplete": function (nButton, oConfig, oFlash) {
                        table.columns( [0, 5] ).visible( false );
                        $(window).keyup(closePrintView);
                    }
                },
                {
                    "sExtends": "pdf",
                    "fnComplete": function (nButton, oConfig, oFlash) {
                        table.columns( [0, 5] ).visible( false );
                        $(window).keyup(closePrintView);
                    }
                },
                {
                    "sExtends": "print",
                    "fnComplete": function (nButton, oConfig, oFlash) {
                        table.columns( [0, 5] ).visible( false );
                        $(window).keyup(closePrintView);
                    }
                },
                {
                    "sExtends":    "refresh",
                    "sButtonText": "Refresh All Current Bookbyte Price",
                    "sDiv":        "div-refresh",
                },
                {
                    "sExtends":    "delete_all",
                    "sButtonText": "Delete All",
                    "sDiv":        "div-delete",
                }
            ]
        }
      });
    });
 
    TableTools.BUTTONS.refresh = $.extend( true, {}, TableTools.buttonBase, {
        "sAction":"div",
        "sTag":"default",
        "sToolTip":"Refresh All Current Bookbyte Price",
        "sNewLine": "<br>",
        "sButtonText": "Delete",
        //"sDiv": "",
        "fnClick": function( nButton, oConfig ) {
            $.ajax({
                url : "ajaxFlipLink.php?getKeys=true",
                type: "POST",
                dataType: "JSON",
                success: function(keys){
                    window.allKey = keys;
                    startScrape();
                },
                error: function (jqXHR, textStatus, errorThrown){
                    swal('Error adding / update data');
                }
            });
        }
    });
 
    TableTools.BUTTONS.delete_all = $.extend( true, {}, TableTools.buttonBase, {
        "sAction":"div",
        "sTag":"default",
        "sToolTip":"Delete All Data",
        "sNewLine": "<br>",
        "sButtonText": "Delete",
        //"sDiv": "",
        "fnClick": function( nButton, oConfig ) {
            delete_data("all");
        }
    });

    var closePrintView = function(e) {
        if(e.which == 27) {
            printViewClosed(); 
        }
    };
         
    function printViewClosed() {
        table.columns( [5] ).visible( true );
        $(window).unbind('keyup', closePrintView);
    }

    function add_data(){
      save_method = 'add';
      $('#form')[0].reset(); // reset form on modals
      $('#modal_form').modal({backdrop: 'static', keyboard: false}); // show bootstrap modal
      $('.modal-title').text('Add New Number'); // Set Title to Bootstrap modal title
      var id = $('#lastID').val();
      $("input[name='id']").val(+id+1);
    }
 
    function edit_data(id){
      save_method = 'update';
      $('#form')[0].reset(); // reset form on modals
 
      //Ajax Load data from ajax
      $.ajax({
        url : "ajaxFlipLink.php?edit=true&id=" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data){
          if(data.length>0){
            var data = data[0];
            $('[name="id"]').val(data.id);
            $('[name="isbn_number"]').val(data.isbn_number);
            $('[name="custom_price"]').val(data.custom_price);
            $('[name="real_price"]').val(data.real_price);
            $('[name="difference"]').val(data.difference);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Data ISBN'); // Set title to Bootstrap modal title
          }
 
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal('Error get data from ajax');
        }
    });
    }
 
    function reload_table(){
      table.ajax.reload(null,false); //reload datatable ajax
    }
 
    function save(){
      var url;
      if(save_method == 'add'){
          url = "ajaxFlipLink.php?add=true";
      }else{
        url = "ajaxFlipLink.php?update=true";
      }
 
       // ajax adding data to database
        $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data){
               //if success close modal and reload ajax table
               $('#modal_form').modal('hide');
               reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown){
                swal('Error adding / update data');
            }
        });
    }
 
    function delete_data(id){
      if(confirm('Are you sure delete this data?')){
        // ajax delete data to database
          $.ajax({
            url : "ajaxFlipLink.php?delete=true&id="+id,
            type: "POST",
            dataType: "JSON",
            success: function(data){
               //if success reload ajax table
               $('#modal_form').modal('hide');
               reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown){
                swal('Error adding / update data');
            }
        });
      }
    }

    function getRealPrice(){
      var isbn_number = $("input[name='isbn_number']").val();
      var custom_price = $("input[name='custom_price']").val();
      var real_price = $("input[name='real_price']").val();
      var difference = $("input[name='difference']").val();
      if(isbn_number && !real_price){
        $('.modal-content').append('<button class="btn btn-lg btn-warning btn-loading" onclick="return false">'
          +'<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>'
          +' Loading...</button><div id="block"></div>');
        $.ajax({
            url : "ajaxFlipLink.php?getRealPrice=true&id="+isbn_number,
            type: "POST",
            dataType: "JSON",
            success: function(res){
              if(res.error=="0"){
                var html = $.parseHTML(res.msg
                    .replace(/<img[^>]*>/g,"")
                    .replace(/<link[^>]*>/g,"")
                    .replace(/<script[^>]*>/g,""));
                var real_price = $("table.gvItemsBuyback table", html).eq(0)
                  .find("td div span").text()
                  .match(/\d.\./)[0].replace('.','');
                $("input[name='real_price']").val(real_price);
                var difference = (+real_price)-(+custom_price);
                $("input[name='difference']").val(difference);
                $('.modal-content .btn-loading').remove();
                $('.modal-content #block').remove();
              }else{
                swal(res.msg);
              }
            },
            error: function (jqXHR, textStatus, errorThrown){
                swal('Error adding / update data');
            }
        });
      }else if(custom_price && real_price){
        var difference = (+real_price)-(+custom_price);
        $("input[name='difference']").val(difference);
      }
    }
 
    $('#newNumber a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
    });

    window.allData = {};
    allData.vat = {};

    // Initialize the widget when the DOM is ready
    var uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'pickfiles',
      container: document.getElementById('pickfiles-container'),
      url : 'readCsv.php',
      filters : {
        max_file_size : '10mb',
        mime_types: [
          {title : "CSV Files", extensions : "csv"}
        ]
      },
      flash_swf_url : 'plupload/js/Moxie.swf',
      silverlight_xap_url : 'plupload/js/Moxie.xap',
      multipart: true,
      multipart_params: {
        action: "read_csv"
      },
      file_data_name: "async-upload",
      init : {
        FilesAdded: function(up, files) {
          $('#console').show();
          $('#console').html('<div class="title">Reading the CSV File.</div>');
          if($('#cekImportNumber:checked').val()){
            uploader.setOption("url","readCsv.php?importNumber=true");
          }else{
            uploader.setOption("url","readCsv.php");
          }
              up.refresh();
              up.start();
        },
        FileUploaded: function(up, file, response) {
          try{
            var res = JSON.parse(response.response);
            if(res.error!="1"){
              window.allKey = res.msg;
              startScrape();
            }else{
              swal(res.msg);
              console.log(res);
            } 
          }catch(e){
            swal("Error: "+e.message);
            console.log(e.stack);
          }
        }
      },
    });
    uploader.init();

    function startScrape(){
      $('body').append('<button class="btn btn-lg btn-warning btn-loading-full" onclick="return false">'
        +'<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>'
        +' Loading...</button><div id="block-full"></div>');
      var i = j = 0;
      window.cekEmpty = "";
      window.cekSucces = "";
      allKey.reduce(function(sequence, key) {
          return sequence.then(function() {
            console.log("key", key);
            return getPage(key);
          })
          .then(function(res){
            return new Promise(function(resolve, reject){
              if(i>=(allKey.length-1)){
                if(cekEmpty){
                  swal({   
                    title: "Error Scrape",   
                    text: cekEmpty,
                    html: true
                  });
                }
                return resolve(finishScrape());
              }
              i++;
              return resolve(res);
            });
          })
          .catch(function(err){
            console.log(err.stack);
            i++;
            return Promise.reject(err);
          })
      }, Promise.resolve());
    }

    function finishScrape(){
      $('.btn-loading-full').remove();
      $('#block-full').remove();
      reload_table();
    }

    function changeFormat(id){
      if(id.length<10){
        id = "0"+id;
        return changeFormat(id);
      }else{
        return id;
      }
    }

    function getPage(ids){
      isbn_number = ids["isbn"].match(/\d/g);
      isbn_number = isbn_number.join("");
      isbn_number = changeFormat(isbn_number);
      return new Promise(function(resolve, reject){
        $.ajax({
          url : "ajaxFlipLink.php?getRealPrice=true&id="+isbn_number,
          type: "POST",
          dataType: "JSON",
          success: function(res){
            if(res.error=="0"){
              var html = $.parseHTML(res.msg
                  .replace(/<img[^>]*>/g,"")
                  .replace(/<link[^>]*>/g,"")
                  .replace(/<script[^>]*>/g,""));
              var err = $("#ctl00_ContentPlaceHolder1_trSpecialMessage", html).text().trim();
              if(err){
                cekEmpty += "<br>"+err;
                swal({   
                  title: "Error Scrape",   
                  text: cekEmpty,
                  html: true
                });
                resolve();
              }else{
                var real_price = $("table.gvItemsBuyback table", html).eq(0)
                  .find("td div span").text()
                  .split("$")[1];
                var id = $("#lastID").val();
                var options = {
                  id: (+id)+1,
                  isbn_number: isbn_number,
                  custom_price: ids["buy"] || 0,
                  real_price: real_price
                }
                saveData(options, function(id){
                  $("#lastID").val(id);
                  cekSucces += "<br>"+isbn_number+" is available. Sell Price: $"+real_price
                  swal({   
                    title: "success Scrape",   
                    text: cekSucces,
                    html: true
                  });
                  return resolve();
                });
              }
            }else{
              cekEmpty += "<br>Response scrape false!";
              swal({   
                title: "Error Scrape",   
                text: cekEmpty,
                html: true
              });
              return resolve();
            }
          },
          error: function (jqXHR, textStatus, errorThrown){
              swal('Error adding / update data');
          }
        });
      })
      .catch(function(err){
        console.log(err.stack);
        return Promise.reject();
      });
    }

    function saveData(options, cb){
      var custom_price = 0;
      var difference = 0;
      if(options.custom_price){
        custom_price = +options.custom_price;
        difference = (+options.real_price) - custom_price;
      }
      var data = "id="+options.id
        +"&isbn_number="+options.isbn_number
        +"&custom_price="+custom_price
        +"&real_price="+options.real_price
        +"&difference="+difference
      $.ajax({
          url : "ajaxFlipLink.php?replace=true",
          type: "POST",
          data: data,
          dataType: "JSON",
          success: function(res){
             $('#modal_form').modal('hide');
             reload_table();
             cb(+(res.data.id)+1);
          },
          error: function (jqXHR, textStatus, errorThrown){
              swal('Error adding / update data');
          }
      });
    }

    function add_data_textarea(){
      var val = $("textarea[name='list-number']").val();
      if(val){
        val = val.split(",");
        window.allKey = [];
        for(var i in val){
          allKey.push({ isbn: val[i], buy: 0 });
        }
        return startScrape();
      }else{
        return swal("ISBNs number is required!");
      }
    }
  </script>
 
  <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal">
          <div class="form-body">
            <div class="form-group" style="display:none">
              <label class="control-label col-md-3">ID</label>
              <div class="col-md-9">
                <input name="id" placeholder="ID" class="form-control" type="number" readonly="true">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">ISBN Number</label>
              <div class="col-md-9">
                <input name="isbn_number" placeholder="ISBN Number" class="form-control" type="number" onchange="getRealPrice();">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Custom Price</label>
              <div class="col-md-9">
                <input name="custom_price" placeholder="Custom Price" class="form-control" type="number" onchange="getRealPrice();">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Real Price</label>
              <div class="col-md-9">
                <input name="real_price" placeholder="Custom Price" class="form-control" type="number" readonly="true">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Difference</label>
              <div class="col-md-9">
                <input name="difference" placeholder="Custom Price" class="form-control" type="number" readonly="true">
              </div>
            </div>
          </div>
        </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
  </body>
</html>