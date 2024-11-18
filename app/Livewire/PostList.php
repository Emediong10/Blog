<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class PostList extends Component
{
use WithPagination ;
public $search ='';
// public $sort;
#[Url()]
public $sort = 'desc';

#[Url()]
public $category ='';

public function setSort($sort)
{
$this->sort =  ($sort ==='desc') ? 'desc' :'asc';
}


#[On('search')]
public function updatedSearch($search)
{
$this->search = $search;
}

public function clearFilters()
{
    $this->search = '';
    $this->category = '';
    $this->reset();
}

    #[Computed()]
    public function posts()
    {
        return Post::take(5)->orderBy('published_at', $this->sort)
        ->when($this->activeCategory, function($query){
            $query->withCategory($this->category);
        })
        ->where('title', 'like', "%{$this->search}%")
        ->paginate(3);
    }
    #[Computed()]
    public function activeCategory()
    {
        return Category::where('slug')->first();
    }

    public function mount($category = null )
{
    $this->category = $category;
}

    public function render()
    {
        return view('livewire.post-list');
    }
}
