@extends('layout')

@section('content')


    <div class="border-y border-black w-full px-1 pt-5 sm:mt-10 sm:pb-6 sm:pt-10 text-center">
        <h1 class="text-5xl">Musicians DB</h1>
        <div class="text-right">
            <h2 class="text-xl pb-1 px-5 sm:p-0 sm:mr-12">Add a Musician</h2>
        </div>
    </div>


    <form action="/add_musician" method="post">@csrf
    <div class="px-1 sm:px-36">

        <div class="grid grid-cols-1 sm:grid-cols-2 sm:grid-rows-2 bg-zinc-300 px-8 py-6 sm:pt-6 sm:pb-0">
            <div class="order-1 sm:order-1 mb-6 sm:mb-0">
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="border-l-2 border-black p-1" placeholder="..first name" />
            </div>

            <div class="order-3 sm:order-2">
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="border-l-2 border-black p-1" placeholder="..last name" />
            </div>

            <div class="order-2 sm:order-3">
                @error('first_name')
                    <p class="text-red-700 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="order-4 sm:order-4">
                @error('last_name')
                    <p class="text-red-700 text-xs">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Instruments:
        </div>


        <div class="p-3 bg-zinc-300 text-center">
            @foreach ($instruments as $instrument)
                <label class="inline-block bg-black text-white m-1 p-1 text-xs add_instrument_btn whitespace-nowrap" id="addbtn_{{ $instrument->id }}">
                    {{ $instrument->name }} <input type="checkbox" name="instrument[{{ $instrument->id }}]">
                </label>
            @endforeach
            @error('instrument')
                <p class="text-red-700 text-xs">Don't forget to select an instrument</p>
            @enderror
        </div>



        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Profile:
        </div>

        <div class="bg-zinc-300 p-3">
            <textarea name="profile_text" class="w-full h-32 border-l-2 border-black p-1"></textarea>
        </div>




        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Details:
        </div>

        <div class="bg-zinc-300 p-3">
            <div id="detail_forms">
                <div class="flex flex-row my-2">
                    <div class="basis-1/4 mr-2">
                        <select name="detail_types[]" class="border-l-2 border-black bg-white p-1">
                            @foreach ($detail_types as $detail_type)
                                <option value="{{ $detail_type->id }}">{{ $detail_type->detail_type_text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="basis-3/4">
                        <input type="text" name="musician_detail[]" class="border-l-2 border-black bg-white text-sm p-1 w-full">
                    </div>
                </div>
            </div>
            <button class="border-l-2 border-black bg-white p-1 px-2" id="add_detail">+</button>
        </div>

        <div class="my-8 flex justify-around">
            <button type="submit" class="border-l-4 border-black  bg-zinc-500 text-white p-1">ADD</button>
            <button onclick="location.href='/?page=<?= request('page') ?>'; return false;" class="border-l-4 border-black  bg-zinc-500 text-white p-1">CANCEL</button>
        </div>








    </div>
    </form>






    <script>

        // when button is clicked, add extra forms for musician details
        let details_form = '<div class="flex flex-row my-2"><div class="basis-1/4 mr-2"><select name="detail_types[]" class="border-l-2 border-black bg-white p-1">\
            @foreach ($detail_types as $detail_type)\
            <option value="{{ $detail_type->id }}">{{ $detail_type->detail_type_text }}</option>\
            @endforeach\
            </select></div>\
            <div class="basis-3/4"><input type="text" name="musician_detail[]" class="border-l-2 border-black bg-white text-sm p-1 w-full"></div></div></div>';

        $('#add_detail').click(function () {
            event.preventDefault();
            $('#detail_forms').append(details_form);
        });


    </script>


@endsection



