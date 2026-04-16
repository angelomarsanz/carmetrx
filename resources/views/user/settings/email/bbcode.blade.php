<div class="col-lg-5">
    <table class="table table-striped border">
        <thead>
            <tr>
                <th scope="col"> {{  __('BB Code') }}</th>
                <th scope="col">{{  __('Meaning') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($templateInfo->mail_type == 'verify_email')
                <tr>
                    <td>{username}</td>
                    <td scope="row">{{   __('Username of User') }}</td>
                </tr>
            @endif

            @if ($templateInfo->mail_type == 'verify_email')
                <tr>
                    <td>{verification_link}</td>
                    <td scope="row">{{   __('Email Verification Link') }}</td>
                </tr>
            @endif

            @if (
                $templateInfo->mail_type == 'reset_password' ||
                    $templateInfo->mail_type == 'course_enrolment' ||
                    $templateInfo->mail_type == 'course_enrolment_approved' ||
                    $templateInfo->mail_type == 'course_enrolment_rejected')
                <tr>
                    <td>{customer_name}</td>
                    <td scope="row">{{   __('Name of The Customer') }}</td>
                </tr>
            @endif

            @if ($templateInfo->mail_type == 'reset_password')
                <tr>
                    <td>{password_reset_link}</td>
                    <td scope="row">{{   __('Password Reset Link') }}</td>
                </tr>
            @endif

            <tr>
                <td>{website_title}</td>
                <td scope="row">{{ __('Website Title') }}</td>
            </tr>
        </tbody>
    </table>
</div>
