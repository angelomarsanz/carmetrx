@if ($cookieConsentConfig['enabled'] && !$alreadyConsentedWithCookies)
    <div class="js-cookie-custom cookie-consent">

        <div class="container">
            <span class="cookie-message">
                {{ strip_tags($cookieAlertInfo->cookie_alert_text) }}
            </span>

            <button class="js-cookie-custom-agree cookie-agree btn btn-md btn-primary">
                {{ $cookieAlertInfo->cookie_alert_btn_text }}
            </button>

        </div>

        @php
            $c = parse_url(url()->current());
            $h = $host = $c['host'];
            $path = $c['path'] ?? '/';
            $p = explode('/', trim($path, '/'));
            $u = $p[0] ?? '';
        @endphp

    </div>

    <script>
        'user-strict'
        window.laravelCookieConsent = (function() {

            let path = "{{ $path }}";
            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = (path == 0) ? "{{ $h . '/' . $u }}" :
                "{{ config('session.domain') ?? request()->getHost() }}";



            function consentWithCookies() {
                setCookie('{{ $cookieConsentConfig['tenant_cookie_name'] }}', COOKIE_VALUE,
                    {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            function cookieExists(name) {
                return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-custom');

                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value +
                    ';expires=' + date.toUTCString() +
                    ';domain=' + COOKIE_DOMAIN +
                    ';path=/{{ config('session.secure') ? ';secure' : null }}' +
                    '{{ config('session.same_site') ? ';samesite=' . config('session.same_site') : null }}';
            }

            if (cookieExists('{{ $cookieConsentConfig['cookie_name'] }}')) {
                hideCookieDialog();
            }

            const buttons = document.getElementsByClassName('js-cookie-custom-agree');

            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>
@endif
