<!-- Product Info -->
<div class="col-lg-8 col-md-6 pb-5 product-info-block">
    <div class="product-info dt-sl">
        <div class="product-title">
            <h1>{{ $product->title }}</h1>
            <h3>{{ $product->title_en }}</h3>
            @can('products.update')
                <a class="btn btn-warning btn-sm admin-edit-url" target="_blank" href="{{ route('admin.products.edit', ['product' => $product]) }}"><i class="mdi mdi-pencil"></i> ویرایش</a>
            @endcan
        </div>

        <div class="row pt-4">
            <div class="col-md-6">
                @if ($product->short_description)
                    <p class="little-des pt-0 mt-0">{!! nl2br($product->short_description) !!}</p>
                @endif
                

                @php
                    $specialSpecifications = $product->specialSpecifications();
                @endphp

                @if($specialSpecifications->count())
                    <div class="product-params dt-sl">
                        <ul class="mt-0" data-title="ویژگی‌های محصول">
                            @foreach($specialSpecifications as $specification)
                                <li>
                                    <span>{{ $specification->name }}: </span>
                                    <span> {{ $specification->pivot->value }} </span>
                                </li>

                            @endforeach
                        </ul>
                        @if($specialSpecifications->count() > 2)
                            <div class="sum-more">
                                    <span class="show-more btn-link-border">
                                        + موارد بیشتر
                                    </span>
                                <span class="show-less btn-link-border">
                                        - بستن
                                    </span>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($product->brand)
                    <div class="d-block mb-2">
                        <span class="font-weight-bold">برند:</span>
                        <a href="{{ route('front.brands.show', ['brand' => $product->brand]) }}" class="link--with-border-bottom">{{ $product->brand->name }}</a>
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                @if ($product->labels->count())
                    <div class="row mr-1 mb-2">
                        <div class="btn-group" role="group">
                            @foreach ($product->labels as $label)
                                <span class="btn-border badge text-white ml-1 bg-primary">{{ $label->title }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($product->isPhysical() && $product->getPrices->count())

                    @php
                        $prev_attribute = null;
                        $groups         = null;
                        $attributes_id  = [];
                    @endphp

                    @foreach ($attributeGroups as $attributeGroup)

                        @if ($product->get_attributes($attributeGroup, $prev_attribute, $groups, $attributes_id))

                            @php
                                $checked       = false;
                                $group_checked = false;
                            @endphp


                            <div class="product-variant dt-sl">
                                <div class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                    <h2 class="d-block">{{ $attributeGroup->name }}:</h2>
                                </div>
                                <ul class="product-variants float-right ml-3">
                                    @foreach ($product->get_attributes($attributeGroup, $prev_attribute, $groups, $attributes_id) as $attribute)
                                        <li class="ui-variant product-attribute">
                                            <label class="ui-variant ui-variant--color">

                                                @if ($attributeGroup->type == 'color')
                                                    <span class="ui-variant-shape" style="background-color: {{ $attribute->value }}"></span>
                                                @endif

                                                @php

                                                    if ($selected_price->get_attributes()->find($attribute->id)) {
                                                        $checked         = true;
                                                        $prev_attribute  = $attribute;
                                                        $attributes_id[] = $attribute->id;
                                                        $group_checked   = true;
                                                    } else {
                                                        $checked = false;
                                                    }

                                                    if ($loop->last && $checked == false && $group_checked == false) {
                                                        $checked = true;
                                                        $prev_attribute  = $attribute;
                                                        $attributes_id[] = $attribute->id;
                                                    }

                                                @endphp

                                                <input data-product="{{ $product->slug }}" type="radio" value="{{ $attribute->id }}" name="attributes_group[{{ $loop->parent->iteration }}][]" class="variant-selector" {{ $checked ? 'checked' : '' }}>
                                                <span class="ui-variant--check {{ $attributeGroup->type != 'color' ? 'product-warranty-span' : '' }}">{{ $attribute->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @php
                                $groups[] = $attributeGroup;
                            @endphp
                        @endif

                    @endforeach

                    @php
                        $selected_price = $product->getPriceWithAttributes($attributes_id)
                    @endphp
                @endif
            </div>
        </div>

        @if($product->isPhysical() && $product->addableToCart())

            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                <h2>قیمت : <span class="price">{{ number_format($selected_price->discountPrice()) }} {{ option('default_currency_id',"ریال") }} </span>
                    @if ($selected_price->discount)
                        <del class="text-danger">{{ number_format($selected_price->tomanPrice()) }} {{ option('default_currency_id',"ریال") }}</del>
                    @endif
                </h2>
            </div>

        @endif

        <div class="dt-sl mt-4">

            @if($product->isPhysical() && $product->addableToCart())
                <div class="mb-2 d-inline">
                    <span class="d-block">{{ $product->getUnit() }}:</span>
                    <div class="number-input">
                        <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()"></button>
                        <input id="cart-quantity" class="quantity" min="1" max="10000" value="1" type="number" required>
                        <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                    </div>
                </div>

                <button data-price_id="{{ $selected_price->id }}" data-action="{{ route('front.cart.store', ['product' => $product]) }}" data-product="{{ $product->slug }}" type="button" class="btn-primary-cm btn-with-icon add-to-cart">
                    <img data-src="{{ theme_asset('img/theme/shopping-cart.png') }}" alt="">
                    افزودن به سبد خرید
                </button>

            @elseif (!$product->addableToCart())
                <button id="stock_notify_btn" data-user="{{ auth()->check() ? auth()->user()->id : '' }}" data-product="{{ $product->id }}" type="button" class="btn-primary-cm bg-secondary btn-with-icon ">
                    <i class="mdi mdi-information"></i>
                    موجود شد به من اطلاع بده
                </button>
            @endif
        </div>
    </div>
</div>
