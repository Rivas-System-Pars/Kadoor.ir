
@if(!$category->getCategoriesCount())
    <li>
        <a href="{{ $category->link }}">{{ $category->title }}</a>
    </li>
@else

    <li class="sub-menu">
        <a href="{{ $category->link }}">{{ $category->title }}</a>
        <ul>
            <li>
                <a class="text-nowrap" href="{{ $category->link }}"><i class="mdi mdi-chevron-left"></i>همه موارد این دسته</a>
            </li>

            @foreach ($category->getCategories() as $childCategory)
                @include('front::partials.mobile-menu.child-category', ['category' => $childCategory])
            @endforeach
        </ul>
    </li>
@endif
