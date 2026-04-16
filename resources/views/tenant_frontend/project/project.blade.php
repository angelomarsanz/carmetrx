 @forelse ($projects as $project)
     <x-tenant.frontend.project :project="$project" class="col-lg-4 col-sm-6" data-aos="fade-up" data-aos-delay="100" />
 @empty
     <div class="col-lg-12">
         <h3 class="text-center mt-5">{{ $keywords['No Project Found'] ?? __('No Project Found') }}</h3>
     </div>
 @endforelse
 <div class="row">
     <div class="col-lg-12 pagination justify-content-center customPaginagte">
         {{ $projects->links() }}
     </div>
 </div>
