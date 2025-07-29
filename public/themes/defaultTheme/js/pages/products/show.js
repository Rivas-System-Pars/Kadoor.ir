$(document).on('click', '.add-to-cart', function () {
    var btn = this;
    var groups = [];

    $('.product-info-block input:checked').each(function (index, el) {
        groups.push($(el).val());
    });

    $.ajax({
        type: 'POST',
        url: $(btn).data('action'),
        data: {
            quantity: $('#cart-quantity').val(),
            price_id: $(btn).data('price_id')
        },
        success: function (data) {
            if (data.status == 'success') {
                Swal.fire({
                    type: 'success',
                    title: 'با موفقیت اضافه شد',
                    text: 'محصول مورد نظر با موفقیت به سبد خرید شما اضافه شد برای رزرو محصول سفارش خود را نهایی کنید.',
                    confirmButtonText: 'باشه',
                    footer: '<h5><a href="/cart">مشاهده سبد خرید</a></h5>'
                });

                $('#cart-list-item').replaceWith(data.cart);
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'خطا',
                    text: data.message,
                    confirmButtonText: 'باشه',
                    footer: '<h5><a href="/cart">مشاهده سبد خرید</a></h5>'
                });
            }
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block(btn);
        },
        complete: function () {
            unblock(btn);
        }
    });
});

$('#stock_notify_btn').click(function () {
    var btn = this;

    if ($(btn).data('user')) {
        sendStockNotify();
    } else {
        $('#modal-stock-notify').modal('show');
    }
});

function sendStockNotify() {
    var btn = $('#stock_notify_btn');

    if ($(btn).data('user')) {
        var data = {
            product_id: $(btn).data('product')
        };
    } else {
        var data = {
            product_id: $(btn).data('product'),
            name: $('#stock-name').val(),
            mobile: $('#stock-mobile').val()
        };
    }

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/stock-notify',
        data: data,
        success: function (data) {
            toastr.success(
                'نام شما در لیست اطلاع از موجودی این محصول قرار گرفت.',
                '',
                {
                    positionClass: 'toast-bottom-left',
                    containerId: 'toast-bottom-left'
                }
            );
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block(btn);
        },
        complete: function () {
            unblock(btn);
        }
    });
}

$('#sendStockNotifyBtn').click(sendStockNotify);

// product prices js codes

$(document).on('click', '.product-attribute', function () {
    var input = $(this).find('input');

    if (input.is(':checked')) {
        return;
    }

    setTimeout(() => {
        var product = input.data('product');
        var groups = [];

        $('.product-info-block input:checked').each(function (index, el) {
            groups.push($(el).val());
        });

        $.ajax({
            type: 'GET',
            url: BASE_URL + '/product/' + product + '/prices',
            data: {
                groups: groups
            },
            success: function (data) {
                setTimeout(() => {
                    $('.product-info-block').replaceWith(data);
                }, 200);
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    $('meta[name="csrf-token"]').attr('content')
                );
                block('.product-info');
            },
            complete: function () {
                unblock('.product-info');
            }
        });
    }, 50);
});

//-------------------------- Add to favorites
$(document).on('click', '#add-to-favorites', function () {
    var btn = this;

    $.ajax({
        type: 'POST',
        url: $(btn).data('action'),
        data: {
            product_id: $(btn).data('product')
        },
        success: function (data) {
            toastr.success('با موفقیت انجام شد', '', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });

            if (data.action == 'create') {
                $(btn).addClass('favorites');
                $(btn).parent().find('span').text('حذف از علاقمندی ها');
            } else {
                $(btn).removeClass('favorites');
                $(btn).parent().find('span').text('افزودن به علاقمندی ها');
            }
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block('#add-to-favorites');
        },
        complete: function () {
            unblock('#add-to-favorites');
        }
    });
});

//-------------------------- tabs
$(document).ready(function () {
    $('.tabs-product-info .ah-tab-item:first').trigger('click');
});

$('#price-changes-modal').on('show.bs.modal', function (e) {
    if (!$(this).find('.chart-prices-label label.active').length) {
        setTimeout(() => {
            $(this).find('.chart-prices-label label').first().trigger('click');
        }, 100);
    }
});

$('.chart-prices-label label').on('click', function () {
    if ($(this).hasClass('active')) {
        return;
    }

    $('#selected-chart-price-title').text($(this).data('title'));

    $('.chart-prices-label label').removeClass('active');
    $(this).addClass('active');

    var action = $(this).data('action');

    $.ajax({
        url: action,
        type: 'GET',
        success: function (data) {
            data = data.data;

            var categories = [];
            var discountPrices = [];
            var realPrices = [];
            var discounts = [];

            for (const [key, value] of Object.entries(data)) {
                categories.push(value.date);
                discountPrices.push(value.discount_price);
                discounts.push(value.discount);

                if (
                    value.discount_price == value.price &&
                    (data[key - 1] == undefined ||
                        data[key - 1].discount_price == data[key - 1].price) &&
                    (data[parseInt(key) + 1] == undefined ||
                        data[parseInt(key) + 1].discount_price ==
                            data[parseInt(key) + 1].price)
                ) {
                    realPrices.push(null);
                } else {
                    realPrices.push(value.price);
                }
            }

            renderPriceChart(
                discountPrices.reverse(),
                realPrices.reverse(),
                discounts.reverse(),
                categories.reverse()
            );
        },

        beforeSend: function (xhr) {
            block('#price-changes-modal .modal-dialog');
        },
        complete: function () {
            unblock('#price-changes-modal .modal-dialog');
        },
        contentType: false,
        processData: false
    });
});

var chart;

//---------------------- modal
function renderPriceChart(discountPrices, realPrices, discounts, categories) {
    if (discountPrices.every((element) => element === null)) {
        $('#chart').hide();
        $('#empty-chart').show();
        return;
    }

    $('#chart').show();
    $('#empty-chart').hide();

    var options = {
        series: [
            {
                name: 'با تخفیف',
                data: discountPrices
            },
            {
                name: 'بدون تخفیف',
                data: realPrices
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            },
            fontFamily: 'iranyekan'
        },

        tooltip: {
            custom: function ({series, seriesIndex, dataPointIndex, w}) {
                if (!series[0][dataPointIndex]) {
                    return '';
                }

                if (discounts[dataPointIndex]) {
                    var discountTemplate = `<div><del>${number_format(
                        series[1][dataPointIndex]
                    )}</del> <span class="chart-tooltip-discount">${
                        discounts[dataPointIndex]
                    }%</span></div>`;
                } else {
                    var discountTemplate = ``;
                }

                return `<div class="chart-tooltip-container">
                    <div class="chart-tooltip-title ml-3">کمترین قیمت:</div>
                    <div class="chart-tooltip-prices">
                        ${discountTemplate}
                        <div class="mt-1"><strong>${number_format(
                            series[0][dataPointIndex]
                        )}</strong> <small> تومان </small></div>
                    </div>
                </div>`;
            }
        },
        stroke: {
            width: [5, 4],
            curve: 'straight',
            dashArray: [0, 5]
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: 0,
                rotateAlways: false,
                formatter: function (value, timestamp, opts) {
                    if (
                        categories[0] == value ||
                        categories[9] == value ||
                        categories[19] == value ||
                        categories[29] == value
                    ) {
                        return value;
                    }

                    return '';
                }
            },
            tooltip: {
                formatter: function (value, timestamp, opts) {
                    return categories[value - 1];
                }
            }
        },
        colors: ['#00bfd6', '#cdcdcd'],
        markers: {
            size: [4, 0]
        },
        yaxis: {
            labels: {
                formatter: (value) => {
                    if (value == null) {
                        return '';
                    }
                    return number_format(value);
                }
            }
        }
    };

    if (chart == undefined) {
        chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    } else {
        chart.destroy();
        chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    }
}
