<div>
    <div class="alert" v-bind:class="[assistant.header_message.alert]" v-html="assistant.header_message.message">@{{ assistant.header_message.message }}</div>


    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#assistant_assistant">Videos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#assistant_docs">Manuals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#assistant_help">Message</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane container active" id="assistant_assistant">
            <br/>
            <div class="row">
                <div class="col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" id="assistant_1_title">@{{ a.assistant.assistant_1_title }}</h3>
                        </div>
                        <div class="card-body" id="assistant_1_body" v-html="a.assistant.assistant_1_body">
                            @{{ a.assistant.assistant_1_body }}
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" id="assistant_2_title">@{{ a.assistant.assistant_2_title }}</h3>
                        </div>
                        <div class="card-body" id="assistant_2_body" v-html="a.assistant.assistant_2_body">
                            @{{ a.assistant.assistant_2_body }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane container" id="assistant_docs">
            <br/>
            <p v-html="a.docs.docs_header">@{{ a.docs.docs_header }}</p>
            <div class="row">
                <div id="assistantdocs" v-if="typeof a.docs.docs_body !== 'undefined' && a.docs.docs_body.length > 0">
                    <div v-if="showLessDocs">
                        <ul>
                            <li v-for="(doc, index) in a.docs.docs_body" :key="doc.post_id" v-if="index <= showDocsCount">
                                <a :href="doc.post_link" target="_blank">@{{ doc.post_title }}</a>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <ul>
                            <li v-for="(doc, index) in a.docs.docs_body" :key="doc.post_id">
                                <a :href="doc.post_link" target="_blank">@{{ doc.post_title }}</a>
                            </li>
                        </ul>
                    </div> 
                    <div>
                        <button class="btn btn-sm btn-outline-primary" @click="showDocsToggle" v-html="showDocsLabel"></button>
                    </div>
                </div>

                <div class="alert alert-warning" v-if="typeof a.docs.docs_body !== 'undefined' && a.docs.docs_body.length === 0">
                    No Documentation Found
                </div>
            </div>
            <br/>
            <p v-html="a.docs.docs_footer">@{{ a.docs.docs_footer }}</p>
        </div>
        
        <div class="tab-pane container" id="assistant_help">
            <br/>
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" id="help_1_title">@{{ a.help.help_1_title }}</h3>
                        </div>
                        <div class="card-body" id="help_1_body">
                            <p>Fill the form below to express your challenges with <strong>@{{ a.help.help_1_body }}</strong> (or anything else) and we&apos;ll reply as quickly as we can</p>
                            <form class="col s12" action="" method="post" enctype="multipart/form-data" v-on:submit.prevent="helpSendMessage">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col s12">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <textarea class="form-control" id="message"  name="message" v-model="helpMessage.message" required></textarea>
                                                <label class="form-label" for="message" class="active">Tell us your challenge</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="form-label">Any Supporting Document(s)?</div>
                                                <div class="custom-file">
                                                    <input type="file" name="attachment" v-on:change="helpAttachmentCheck" id="attachment" ref="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*" class="custom-file-input">
                                                    <label id="attachment_label" class="custom-file-label">Choose Attachment</label>
                                                </div>
                                                <small id="assistant_help_file_message">NOT Compulsory. Any attachment must not exceed 100KB in size</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input type="hidden" name="area" id="area" v-model="helpMessage.area">
                                        <button id="assistant_help_submit" class="btn btn-outline-primary btn-block" type="submit" v-on:submit.prevent="helpSendMessage" name="action">Send Request</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" id="help_2_title">@{{ a.help.help_2_title }}</h3>
                        </div>
                        <div class="card-body" id="help_2_body" v-html="a.help.help_2_body">
                            @{{ a.help.help_2_body }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>
