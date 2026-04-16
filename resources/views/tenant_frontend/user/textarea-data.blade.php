<div class="modal fade" id="textAreaModal-{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ $label }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p class="text-center">{{ $information->value }}</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal">{{ $keywords['close'] }}</button>
      </div>
    </div>
  </div>
</div>
