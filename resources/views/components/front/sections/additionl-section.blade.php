<div>
    <section class="custom-section-area pt-120 pb-90">
        <div class="container">
            <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title mb-0">
                    {{ @$content->section_name }}
                </h2>
            </div>
            <div class="row align-items-center gx-xl-5">
                {!! $content?->content !!}
            </div>
        </div>
    </section>
</div>
