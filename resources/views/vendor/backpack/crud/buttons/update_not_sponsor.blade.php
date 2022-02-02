<a href="javascript:void(0)" onclick="removeSponsorEntries(this)" class="btn btn-danger">
    <span class="ladda-label"><i class="la la-arrow-circle-down"></i></span> Remove Sponsored
</a>

@push('after_scripts')
<script>
  if (typeof removeSponsorEntries != 'function') {
    function removeSponsorEntries(button) {

        if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0)
        {
          new Noty({
                title: "{{ trans('backpack::crud.bulk_no_entries_selected_title') }}",
                text: "{{ trans('backpack::crud.bulk_no_entries_selected_message') }}",
                type: "warning"
            }).show();

          return;
        }

        var message = "Are you sure you want to remove sponsored these :number entries?";
        message = message.replace(":number", crud.checkedItems.length);


        // show confirm message
        swal({
        title: "Information",
        text: message,
        icon: "info",
        buttons: {
          cancel: {
          text: "{{ trans('backpack::crud.cancel') }}",
          value: null,
          visible: true,
          className: "bg-secondary",
          closeModal: true,
        },
          delete: {
          text: "Yes",
          value: true,
          visible: true,
          className: "bg-primary",
        }
        },
      }).then((value) => {
        if (value) {
          var ajax_calls = [];
              var clone_route = "{{ url($crud->route) }}/delete-sponsor";
                // submit an AJAX delete call
                  $.ajax({
                    url: clone_route,
                    type: 'POST',
                    data: { entries: crud.checkedItems },
                    success: function(result) {
                      // Show an alert with the result
                            new Noty({
                            type: "success",
                            text: "<strong>Entries Remove Status Sponsored</strong><br>"+crud.checkedItems.length+" entries have been changed."
                          }).show();

                      crud.checkedItems = [];
                      crud.table.ajax.reload();
                    },
                    error: function(result) {
                      var defaultText = "Gagal meproses data dari server. Silahkan coba lagi / login kembali.";
                      if(result.status != 500 && result.responseJSON != null && result.responseJSON.message != null && result.responseJSON.message.length != 0){
                        defaultText = result.responseJSON.message;
                      }
                      // Show an alert with the result
                            new Noty({
                            type: "danger",
                            text: defaultText
                          }).show();
                    }
                  });
        }
        });
      }
  }
</script>
@endpush