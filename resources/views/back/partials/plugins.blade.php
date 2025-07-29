

@foreach ($plugins as $plugin)

    @switch($plugin)
        @case('ckeditor')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/ckeditor/ckeditor.js') }}"></script>
            @endpush

        @break

        @case('jquery-tagsinput')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/jquery-tagsinput/jquery.tagsinput.min.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/plugins/jquery-tagsinput/jquery.tagsinput.min.css') }}">
            @endpush

        @break

        @case('jquery-tagsinput')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/jquery-tagsinput/jquery.tagsinput.min.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/plugins/jquery-tagsinput/jquery.tagsinput.min.css') }}">
            @endpush

        @break

        @case('datatable')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/datatable/scripts.bundle.js') }}"></script>
                <script src="{{ asset('back/app-assets/plugins/datatable/core.datatable.js') }}"></script>
                <script src="{{ asset('back/app-assets/plugins/datatable/datatable.checkbox.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/plugins/datatable/datatable.css') }}">
            @endpush

        @break

        @case('jquery-ui')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/jquery-ui/jquery-ui.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/plugins/jquery-ui/jquery-ui.css') }}">
            @endpush

        @break

        @case('dropzone')
            @push('scripts')
                <script src="{{ asset('back/app-assets/vendors/js/extensions/dropzone.min.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/vendors/css/file-uploaders/dropzone.min.css') }}">
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/css-rtl/plugins/file-uploaders/dropzone.css') }}">
            @endpush

        @break

        @case('jquery-ui-sortable')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/jquery-ui-sortable/jquery-ui.min.js') }}"></script>
            @endpush

        @break

        @case('persian-datepicker')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/persian-date/persian-date.min.js') }}"></script>
                <script src="{{ asset('back/app-assets/plugins/persian-date/persian-datepicker.min.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" type="text/css" href="{{ asset('back/app-assets/plugins/persian-date/persian-datepicker.min.css') }}">
            @endpush

        @break

        @case('mapp')
            @push('scripts')
                <script type="text/javascript" src="{{ asset('back/app-assets/mapp/js/mapp.env.js') }}"></script>
                <script type="text/javascript" src="{{ asset('back/app-assets/mapp/js/mapp.min.js') }}"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" href="{{ asset('back/app-assets/mapp/css/mapp.min.css') }}">
                <link rel="stylesheet" href="{{ asset('back/app-assets/mapp/css/fa/style.css') }}">
            @endpush

        @break

        @case('apexcharts')
            @push('scripts')
                <script src="{{ asset('back/app-assets/vendors/js/charts/apexcharts.min.js') }}?v=2"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" href="{{ asset('back/app-assets/vendors/js/charts/apexcharts.css') }}">
            @endpush

        @break

        @case('combo-tree')

            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/combo-tree/combo-tree.js') }}?v=1"></script>
            @endpush

            @push('styles')
                <link rel="stylesheet" href="{{ asset('back/app-assets/plugins/combo-tree/combo-tree.css') }}?v=1">
            @endpush

        @break

        @case('google-map')
            @push('scripts')
                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdbCAXvJIl7CKZwfpTswAIHvqJmZTUPwQ"></script>
            @endpush

        @break

        @case('jquery.validate')
            @push('scripts')
                <script src="{{ asset('back/app-assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
                <script src="{{ asset('back/app-assets/plugins/jquery-validation/localization/messages_fa.min.js') }}"></script>
            @endpush

        @break

    @endswitch

@endforeach
