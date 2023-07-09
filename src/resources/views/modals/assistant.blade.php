<div v-if="!loadingAssistant" class="modal fade" id="modules-assistant-modal" tabindex="-1" role="dialog" aria-labelledby="modules-assistant-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modules-assistant-modalLabel">Help Centre</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @component('modules-assistant::shell')    
            <!-- Additional Text To be referenced by-->
        @endcomponent
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-success">Button 1</button> -->
        <!-- <button type="button" class="btn btn-primary"><i class="fe fe-plus mr-2"></i>Options</button> -->
        <!-- <div class="dropdown">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <i class="fe fe-plus mr-2"></i>More Help
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Option 1</a>
            <a class="dropdown-item" href="#">Option 2</a>
          </div>
        </div> -->
      </div>
    </div>
  </div>
</div>