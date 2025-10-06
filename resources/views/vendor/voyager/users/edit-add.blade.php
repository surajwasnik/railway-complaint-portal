@php
   $edit = isset($dataTypeContent) && !is_null($dataTypeContent->getKey());
$add  = !isset($dataTypeContent) || is_null($dataTypeContent->getKey());

@endphp

@extends('voyager::master')
@section('page_title', 'Railway Complaint Portal | ' . ($user ? 'Police station user edit' : 'Police station user add'))

@section('content')
<div class="page-content">
    <h1 class="page-title">{{$user ? __('Police station user edit') : __('Police station user add') }}</h1>
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form method="POST" action="{{ isset($user) && $user ? route('user.update', $user->id) : route('user.store') }}">
                        @csrf

                        <div class="panel-body">
                            <input type="hidden" id="role_id" name="role_id" value="2">

                            {{-- Error Display --}}
                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                           @php
                                $language = optional($user)->language === 'marathi' ? 'marathi' : 'english';
                            @endphp

                             <input type="hidden" name="language" id="language" value="{{ $user->language ?? 'english' }}">

                            {{-- ================= STATION FIELDS ================= --}}
                            <div id="station-fields">
                                <h4>{{ __('Police Station Information') }}</h4>


                                {{-- Tab Contents --}}
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="{{ $language == 'english' ? 'active' : '' }}">
                                    <a href="#english" aria-controls="english" role="tab" data-toggle="tab"
                                       onclick="document.getElementById('language').value='english'">English</a>
                                </li>
                                <li role="presentation" class="{{ $language == 'marathi' ? 'active' : '' }}">
                                    <a href="#marathi" aria-controls="marathi" role="tab" data-toggle="tab"
                                       onclick="document.getElementById('language').value='marathi'">मराठी</a>
                                </li>
                            </ul>



<div class="tab-content" style="margin-top:20px;">
    <!-- English Tab -->
     <div role="tabpanel" class="tab-pane {{ $language == 'english' ? 'active' : '' }}" id="english">
    <div class="form-group">
        <label>Police Station Name</label>
        <input type="text" name="station_name_en" id="station_name_en" class="form-control" placeholder="Police Station Name"
              value="{{ old('station_name_en', ($user && $language == 'english') ? optional($user->station)->station_name : '') }}">
    </div>

    <div class="form-group">
        <label>Head Officer Name</label>
        <input type="text" name="station_head_name_en" id="station_head_name_en" class="form-control" placeholder="Head Officer Name"
           value="{{ old('station_head_name_en', ($user && $language == 'english') ? optional($user->station)->station_head_name : '') }}">
    </div>

    <div class="form-group">
        <label>Mobile Number</label>
        <input type="text" name="station_head_phone_en" id="station_head_phone_en" class="form-control" placeholder="Mobile Number"
           value="{{ old('station_head_phone_en', ($user && $language == 'english') ? optional($user->station)->station_head_phone : '') }}">
    </div>
</div>


    <!-- Marathi Tab -->
    <div role="tabpanel" class="tab-pane {{ $language == 'marathi' ? 'active' : '' }}" id="marathi">
    <div class="form-group">
        <label>पोलीस स्टेशन नाव</label>
        <input type="text" name="station_name_mr" id="station_name_mr" class="form-control translate-to-mr" placeholder="पोलीस स्टेशन नाव"
           value="{{ old('station_name_mr', ($user && $language == 'marathi') ? optional($user->station)->station_name : '') }}">
    </div>

    <div class="form-group">
        <label>पोलीस प्रमुखाचे नाव</label>
        <input type="text" name="station_head_name_mr" id="station_head_name_mr" class="form-control translate-to-mr" placeholder="पोलीस प्रमुखाचे नाव"
           value="{{ old('station_head_name_mr', ($user && $language == 'marathi') ? optional($user->station)->station_head_name : '') }}">
    </div>

    <div class="form-group">
        <label>मोबाईल नंबर</label>
        <input type="text" name="station_head_phone_mr" id="station_head_phone_mr" class="form-control translate-to-mr" placeholder="मोबाईल नंबर"
           value="{{ old('station_head_phone_mr', ($user && $language == 'marathi') ? optional($user->station)->station_head_phone : '') }}">
    </div>
</div>

</div>

                                </div>

                                <div class="form-group mt-3">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control">
    <option value="active" {{ old('status', optional($user?->station)->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
    <option value="inactive" {{ old('status', optional($user?->station)->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    <option value="suspended" {{ old('status', optional($user?->station)->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
</select>

                                </div>
                            </div>
                            
                            <input type="hidden" name="station_name" id="station_name_hidden" value="">
<input type="hidden" name="station_head_name" id="station_head_name_hidden" value="">
<input type="hidden" name="station_head_phone" id="station_head_phone_hidden" value="">


                            {{-- ================= USER CREDENTIALS ================= --}}
                            <div class="panel-body">
                            <h4>{{ __('User Credentials')}}</h4>
                            <div id="user-fields">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" value="{{ $user->email ?? old('email') }}"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="name" id="name" value="{{ $user->name ?? old('name') }}"
                                        class="form-control" id="target" required>
                                </div>

                                <div class="form-group">
                                    <label>Password {{ $user ? '(leave blank to keep old)' : '' }}</label>
                                    <input type="password" name="password" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                            </div>

                            <button type="submit" class="btn btn-{{ $user ? 'success' : 'primary' }}">
                                {{ $user ? 'Update User' : 'Create User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="languageConfirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Language Change</h4>
      </div>
      <div class="modal-body">
        Your data may be lost if you switch the language. Do you want to continue?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelLanguageBtn">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmLanguageBtn">Yes, Continue</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="{{ url('js/custom.js?v=1.0') }}"></script>
// <script>
// document.querySelectorAll('#english input, #marathi input').forEach(field => {
//     field.addEventListener('input', function() {
//         if (field.id === 'station_name_en') document.getElementById('station_name_hidden').value = field.value;
//         if (field.id === 'station_name_mr') document.getElementById('station_name_hidden').value = field.value;

//         if (field.id === 'station_head_name_en') document.getElementById('station_head_name_hidden').value = field.value;
//         if (field.id === 'station_head_name_mr') document.getElementById('station_head_name_hidden').value = field.value;

//         if (field.id === 'station_head_phone_en') document.getElementById('station_head_phone_hidden').value = field.value;
//         if (field.id === 'station_head_phone_mr') document.getElementById('station_head_phone_hidden').value = field.value;
//     });
// });

// document.addEventListener('DOMContentLoaded', function() {
//     let marathiName = document.querySelector('#marathi input[name="station_name"]');
//     let marathiHead = document.querySelector('#marathi input[name="station_head_name"]');

//     async function translateToMarathi(text, field) {
//         if (!text) return;
//         try {
//             const res = await axios.post('https://libretranslate.com/translate', {
//                 q: text,
//                 source: 'en',
//                 target: 'mr',
//                 format: 'text'
//             });
//             field.value = res.data.translatedText;
//         } catch (err) {
//             console.error(err);
//         }
//     }

//     let timeout;
//     [marathiName, marathiHead].forEach(field => {
//         field.addEventListener('input', function() {
//             clearTimeout(timeout);
//             let target = this;
//             timeout = setTimeout(() => {
//                 translateToMarathi(target.value, target);
//             }, 500); // wait 0.5s after typing stops
//         });
//     });
// });
// </script>
<script>
    // function toggleFields() {
    //     let dropdown = document.getElementById("role_id");
    //     let roleName = dropdown.options[dropdown.selectedIndex]?.text.toLowerCase();

    //     document.getElementById("user-fields").style.display = "block";

    //     if (roleName === "super_admin") {
    //         document.getElementById("station-fields").style.display = "none";
    //     } else {
    //         document.getElementById("station-fields").style.display = "block";
    //     }
    // }
    
// document.addEventListener('DOMContentLoaded', function() {
//     // Get saved language from Blade
//     let selectedLanguage = "{{ $user->language ?? 'english' }}";

//     // Update hidden input
//     document.getElementById('language').value = selectedLanguage;

//     // Remove 'active' from all tabs
//     document.querySelectorAll('.nav-tabs li').forEach(li => li.classList.remove('active'));
//     document.querySelectorAll('.tab-content .tab-pane').forEach(pane => pane.classList.remove('active', 'in'));

//     // Activate the correct tab & pane
//     if (selectedLanguage === 'marathi') {
//         document.getElementById('marathi-tab').classList.add('active');
//         document.getElementById('marathi').classList.add('active', 'in');
//     } else {
//         document.getElementById('english-tab').classList.add('active');
//         document.getElementById('english').classList.add('active', 'in');
//     }
// });

let pendingLang = null;
let currentLang = "{{ $language }}"; // 'english' or 'marathi'

// intercept tab clicks
$('a[data-toggle="tab"]').on('click', function (e) {
    let lang = $(this).attr('aria-controls'); // "english" or "marathi"

    @if(isset($edit) && $edit)
        e.preventDefault(); // stop bootstrap from switching tab
        pendingLang = lang;
        $('#languageConfirmModal').modal('show'); // show modal
    @else
        setLanguage(lang);
        currentLang = lang;
    @endif
});

// confirm button
$('#confirmLanguageBtn').on('click', function () {
    if (pendingLang) {
        // clear only CURRENT tab’s fields
        if (currentLang === 'marathi') {
            $('#station_name_en, #station_head_name_en, #station_head_phone_en').val('');
        } else if (currentLang === 'english') {
            $('#station_name_mr, #station_head_name_mr, #station_head_phone_mr').val('');
        }

        // now switch manually
        setLanguage(pendingLang);
        $('[href="#' + pendingLang + '"]').tab('show');

        // update state
        currentLang = pendingLang;
        pendingLang = null;
        $('#languageConfirmModal').modal('hide');
    }
});

$('#cancelLanguageBtn').on('click', function (e) {
    e.preventDefault(); // stop tab switch
    e.stopPropagation(); // stop bubbling

    // reset the tab back to currentLang
    $('[href="#' + currentLang + '"]').tab('show'); 

    pendingLang = null;
    $('#languageConfirmModal').modal('hide');
});


// helper
function setLanguage(lang) {
    $('#language').val(lang);
}

</script>
@endsection