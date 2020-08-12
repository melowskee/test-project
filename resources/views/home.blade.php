@extends('layouts.app')
@section('styles')
<style type="text/css" media="Screen">
	#available_tags kbd{
        cursor:pointer;
        z-index: 9999;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3 d-none">
                <div class="card-header"><h5 class="mb-0">{{ __('Dashboard') }}</h5></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        
                    {{ __('You are logged in!') }}
                </div>
            </div>

            <div class="card  mb-3">
                <div class="card-header text-white bg-dark"><h5 class="mb-0">STEP 1: Upload a csv file</h5></div>
                <div class="card-body">
                    <a name="step1"></a>
                    <p class="text-muted">Upload a csv file containing the email addresses and dynamic field values to get started.</p>
                    <hr>
                    <form role="form" id="form_csv" action="{{route('parse_csv')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" id="csvInputFile" name="csv">
                                <label class="custom-file-label" for="csvInputFile">Choose a CSV file</label>
                            </div>
                            
                            <p class="help-block">The first line of the csv file should contain the column names.</p>
                        </div>
                        <div class="form-group">
                            Delimiter:
                            <label class="radio-inline">
                            <input type="radio" name="delimiter" id="delimiter1" value="," checked> comma (,)
                            </label>
                            <label class="radio-inline">
                            <input type="radio" name="delimiter" id="delimiter2" value=";"> semicolon (;)
                            </label>
                            <label class="radio-inline">
                            <input type="radio" name="delimiter" id="delimiter3" value="|"> pipe (|)
                            </label>
                        </div>
                        <div class="text-right">
                        <button type="submit" class="btn btn-primary">UPLOAD &amp; CONTINUE</button>
                        </div>
                    </form>

                    <div class="card step mt-3" id="step_preview" style="display:none;">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Data Preview</h5>
                        </div>
                        <div class="card-body">
                            <a name="preview"></a>
                            <table class="table table-striped table-bordered" id="table_csv_preview">
                            <thead></thead>
                            <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        

       
               
            <div class="card step mb-3" id="step_compose" style="display:none;">
                <div class="card-header text-white bg-dark">
                    <h5 class="mb-0">Step 2: Compose email</h5>
                </div>
                <div class="card-body">
                    <a name="compose"></a>
                    <p class="text-muted">
                        All text input fields may contain dynamic fields. Use {field_name} where you want to insert the value of a dynamic field.<br/> 
                    </p>
                    <hr>
                    <form role="form" id="form_compose">
                        <input type="hidden" id="information" val="">
                        <div class="form-group">
                            <label for="email_name">Sender name:</label>
                            <input type="text" class="form-control" id="email_name" name="email_name" placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="email_address">Sender email:</label>
                            <input type="email" class="form-control" id="email_address" name="email_address" placeholder="john@doe.com">
                        </div>
                        <div class="form-group">
                            <label for="email_recipient_field">Recipient email field:</label>
                            <select class="form-control" id="email_recipient_field" name="email_recipient_field">
                            </select>
                            <p class="help-block">
                                Select the csv column that contains the email addresses of the recipients.
                            </p>
                        </div>
                        <div class="form-group d-none">
                            <label for="email_cc">CC:</label>
                            <input type="email" class="form-control" id="email_cc" name="email_cc">
                            <p class="help-block">
                                Optionally add one or more CC address(es). Leave empty to ignore.
                            </p>
                        </div>
                        <div class="form-group d-none">
                            <label for="email_bcc">BCC:</label>
                            <input type="email" class="form-control" id="email_bcc" name="email_bcc">
                            <p class="help-block">
                                Optionally add one or more BCC address(es). Leave empty to ignore.
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="email_subject">Subject:</label>
                            <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="Enter the subject line here">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <div class="card">
                                <div class="card-header">
                                        Dynamic Fields
                                    </div>
                                    <div class="card-body">
                                        <p>drag and drop the field/s to the text area.</p>
                                        <div id="available_tags" class="mb-1"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="email_contect">Email body:</label>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        @foreach($campaign->templates as $template)
                                        <label class="badge badge-warning templates" id="template-{{$template->id}}" data-content="{{$template->content}}">{{$template->name}}</label> 
                                        @endforeach 
                                    </div>
                                </div>
                                <textarea name="email_body" id="email_body" class="tags_drop_target ui-droppable form-control" cols="80" rows="15"></textarea>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" id="button_review" class="btn btn-primary">DONE COMPOSING EMAIL</button>
                        </div>
                    </form>
                </div>
            </div>    
                                
            <div class="card step mb-3" id="step_review" style="display:none;">
                <div class="card-header text-white bg-dark">
                    <h5 class="mb-0">Step 3: Review email</h5>
                </div>
                <div class="card-body">
                    <a name="review"></a>
                   
                    <p class="text-muted">
                        The email based on the first data line is shown below. Carefully check if the addressing and dynamic fields are correct.<br/>
                        If everything is OK, press the button to SEND EMAILS.</br>
                        If something is wrong, modify the email and review again.
                    </p>
                    <hr>
                    <blockquote id="email_review_content">
                    </blockquote>
                    <div class="text-right">
                        <button type="button" id="button_send_emails" class="btn btn-danger">SEND EMAILS</button>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="modal fade" id="modal_progress">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white bg-dark">
                <h4 class="modal-title">Sending in progress</h4>
            </div>
            <div class="modal-body">
                <p>The following emails have been sent:</p>
                <p id="sent_list"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="button_progress_close" style="display:none;">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
<script src="{{ asset('js/jquery.form.js') }}"></script>
<!-- <script src="https://cdn.tiny.cloud/1/wnl7s60m73b8g28sah602bzzsb6wfmi0br3d7tofaph0zk5v/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
        window.test_project = new Object(); // Holds some global stuff
        
        $(document).ready(function() {
            $('#form_csv').ajaxForm(function(data) {
                csv = jQuery.parseJSON(data);
                if ('error' in csv) {
                    alert("Error: " + csv.error);
                    return;
                }

                window.test_project.fields = {};
                window.test_project.data = csv.data;

                // csv preview
                var html_thead = "<tr>";
                var html_tags = "";
                var html_recipient_field_select = "";
               
                
                for (i=0; i<csv.column_names.length; i++) {
                    html_thead += "<th>" + csv.column_names[i] + "</th>";
                    html_tags += "<kbd class=\"ui-draggable\">{" + csv.column_names[i] + "}</kbd></br>";
                    window.test_project.fields["{"+csv.column_names[i]+"}"] = i;
                    html_recipient_field_select += "<option value=\"" + i + "\">" + csv.column_names[i] + "</option>";
                }
                html_thead += "</tr>";
                var html_tbody = "";
                for (row=0; row<csv.data.length; row++) {
                    html_tbody += "<tr>";
                    for (col=0; col<csv.data[row].length; col++) {
                        html_tbody += "<td>" + csv.data[row][col] + "</td>";
                    }
                    html_tbody += "</tr>";
                }

                $('.step').hide();
                $('#available_tags').html(html_tags);
                $("#available_tags kbd").draggable({helper: 'clone'});
                $('#table_csv_preview thead').html(html_thead);
                $('#table_csv_preview tbody').html(html_tbody);
                $('#email_recipient_field').html(html_recipient_field_select);
                $('#step_preview').show();
                $('#step_compose').show();
                $('html,body').animate({scrollTop: $('#step_preview').offset().top},'slow');
                $('#information').val(JSON.stringify(csv));
            });

            $('#button_review').on("click", review_email);
            $('#button_send_emails').on("click", send_emails);
            
            $(".templates").on( "click", function() {
                var content = $(this).data('content');
                $('#email_body').text(content);
            });

            // droppable textarea
            $(".tags_drop_target").droppable({
                accept: "#available_tags kbd",
                drop: function(ev, ui) {
                    $(this).insertAtCaret(ui.draggable.text());
                }
            });

            $.fn.insertAtCaret = function (myValue) {
            return this.each(function(){
            //IE support
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            //mozilla support
            else if (this.selectionStart || this.selectionStart == '0') {
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos)+ myValue+ this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
            });
            };
        });

        function replace_dynamic_fields(input_string, data_row) {
            for (var dynamic_field in window.test_project.fields) {
                input_string = input_string.split(dynamic_field).join(window.test_project.data[data_row][window.test_project.fields[dynamic_field]]);
            }

            return input_string;
        }

        function review_email() {
            var email_name = $('#email_name').val();
            var email_address = $('#email_address').val();
            var email_recipient_field = parseInt($('#email_recipient_field').val());
            var email_cc = $('#email_cc').val();
            var email_bcc = $('#email_bcc').val();
            var email_subject = $('#email_subject').val();
            var email_body = $('#email_body').val();

            if (email_name == '') {
                alert("First enter a sender name");
                return;
            }
            if (email_address == '') {
                alert("First enter a sender address");
                return;
            }
            if (email_subject == '') {
                alert("First enter a subject line");
                return;
            }
            if (email_body == '') {
                alert("First enter a message body");
                return;
            }

            var review_html = "From: " + replace_dynamic_fields(email_name, 0) + " &lt;" + replace_dynamic_fields(email_address, 0) + "&gt;<br/>";
            review_html += "To: " + window.test_project.data[0][email_recipient_field] + "<br/>";
            if (email_cc != "") review_html += "Cc: " + replace_dynamic_fields(email_cc, 0) + "<br/>";
            if (email_bcc != "") review_html += "Bcc: " + replace_dynamic_fields(email_bcc, 0) + "<br/>";
            review_html += "Subject: " + replace_dynamic_fields(email_subject, 0) + "<br/><br/>";
            review_html += replace_dynamic_fields(email_body, 0).split("\n").join("<br/>");

            $('#email_review_content').html(review_html);
            $('#step_review').show();
            $('html,body').animate({scrollTop: $('a[name=review]').offset().top},'slow');
        }

        function send_emails() {
            if (!confirm("Are you sure you want to send all emails?")) return;
            $('#sent_list').html("");
            $('#button_progress_close').hide();
            $('#modal_progress').modal('show');
            send_email(0);
        }

        function send_email(idx) {
            // This function automatically sets a timeout to send the next email
            // So calling send_email(3) will send email 3, 4, 5, ... , N
            var post_data = {};
            post_data['_token'] = '{{ csrf_token() }}';
            post_data['from_name'] = replace_dynamic_fields($('#email_name').val(), idx);
            post_data['from_address'] = replace_dynamic_fields($('#email_address').val(), idx);
            post_data['recipient'] = window.test_project.data[idx][parseInt($('#email_recipient_field').val())];
            post_data['cc'] = replace_dynamic_fields($('#email_cc').val(), idx);
            post_data['bcc'] = replace_dynamic_fields($('#email_bcc').val(), idx);
            post_data['subject'] = replace_dynamic_fields($('#email_subject').val(), idx);
            post_data['body'] = replace_dynamic_fields($('#email_body').val(), idx);
            post_data['information'] = $('#information').val();
            post_data['count'] = idx;

            $.post("{{ route('send_email') }}", post_data)
                .done(function(data) {
                    csv = jQuery.parseJSON(data);
                    if ('error' in csv) {
                        $('#sent_list').html($('#sent_list').html() + "<br/><b>ABORTED DUE TO ERROR:</b><br/>" + csv.error);
                        $('#button_progress_close').show();
                        return;
                    } else {
                        $('#sent_list').html($('#sent_list').html() + ' ' + post_data['recipient']+'<br/>');
                        next_idx = idx + 1;
                        if (next_idx < window.test_project.data.length) {
                            setTimeout(function(){send_email(next_idx);}, 1000);
                        } else {
                            $('#sent_list').html($('#sent_list').html() + "<br/><b>Emails sent successfully to the recipients!</b>");
                            $('#button_progress_close').show();
                        }
                    }
                });
        }
    </script>
@endsection