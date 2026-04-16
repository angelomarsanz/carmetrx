{{-- whatsapp init code --}}

@if (!empty($permissions) && in_array('Whatsapp', $permissions) && $basicInfo->whatsapp_status == 1)
    <script type="text/javascript">
        "use strict";
        var whatsapp_popup = "{{ $basicInfo->whatsapp_popup_status }}";
        var whatsappImg = "{{ asset('assets/front/img/whatsapp.svg') }}";

        $(function() {
            $('#WAButton').floatingWhatsApp({
                phone: "{{ $basicInfo->whatsapp_number }}",
                headerTitle: "{{ $basicInfo->whatsapp_header_title }}",
                popupMessage: `{!! nl2br($basicInfo->whatsapp_popup_message) !!}`,
                showPopup: whatsapp_popup == 1 ? true : false,
                buttonImage: '<img src="' + whatsappImg + '" />',
                position: "right"
            });
        });
    </script>
@endif
