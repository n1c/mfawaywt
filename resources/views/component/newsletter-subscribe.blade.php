<div class="row" style="display: none;" id="jsWrapNewsletter">
    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 well">
            <form action="{{ config('services.mailchimp.newslettersubpost') }}" method="POST" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="{{ config('services.mailchimp.newslettersubname') }}" tabindex="-1" value=""></div>

                <div class="input-group">
                    <input type="email" name="EMAIL" class="form-control input-lg" placeholder="Get the newsletter!" id="jsInputNewsletterEmail" autofocus required>
                    <span class="input-group-btn">
                        <input class="btn btn-lg btn-primary" type="submit" value="Subscribe" id="jsButtonNewsletterSubscribe">
                    </span>
                </div>
            </form>
    </div>
</div>

@push('script')
    <script>
        var $wrapNewsletter = $('#jsWrapNewsletter');
        var $inputNewsletterEmail = $('#jsInputNewsletterEmail');
        var $buttonSubscribe = $('#jsButtonNewsletterSubscribe');

        if (!localStorage.getItem('newsletterHide')) {
            $wrapNewsletter.fadeIn(function () {
                $inputNewsletterEmail.focus();
            });
        }

        $buttonSubscribe.on('click', function () {
            $wrapNewsletter.fadeOut();
            localStorage.setItem('newsletterHide', true);
        });
    </script>
@endpush
