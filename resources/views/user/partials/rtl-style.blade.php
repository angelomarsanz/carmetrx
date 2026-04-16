
@if (!empty($currentLang) && $currentLang->rtl == 1)
    @section('styles')
        <style>
            form:not(.modal-form.create) input,
            form:not(.modal-form.create) textarea,
            form:not(.modal-form.create) select {
                direction: rtl;
            }

            form:not(.modal-form.create) .note-editor.note-frame .note-editing-area .note-editable {
                direction: rtl;
                text-align: right;
            }
        </style>
        @if (request()->routeIs('user.contact'))
            <style>
                form:not(.modal-form) input {
                    direction: ltr;
                }
            </style>
        @endif
    @endsection
@endif
