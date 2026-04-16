<?php

namespace App\View\Components\Front\Sections;

use Closure;
use App\Models\BasicExtended;
use App\Models\Package;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pricing extends Component
{
    public $featured; // vale must be  page or section
    /**
     * Create a new component instance.
     */
    public function __construct($featured = false)
    {
        $this->featured = $featured;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $packages = Package::where('status', '1')->get();
        if ($this->featured) {
            $packages  = $packages->where('featured', '1');
        }
        // Extract unique terms from packages
        $availableTerms = $packages->pluck('term')->unique()->toArray();
        $terms = array_intersect(['monthly', 'yearly', 'lifetime'], $availableTerms);
        $terms = array_map('ucfirst', $terms); // Convert to "Monthly", "Yearly", "Lifetime"

        $be = BasicExtended::select('package_features')->firstOrFail();
        $allPfeatures = json_decode($be->package_features ?? '[]', true);

        return view('components.front.sections.pricing', compact('terms', 'allPfeatures', 'be', 'packages'));
    }
}
