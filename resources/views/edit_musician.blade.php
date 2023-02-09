@extends('layout')

@section('content')


    <div class="border-y border-black w-full px-1 pt-5 sm:mt-10 sm:pb-6 sm:pt-10 text-center">
        <h1 class="text-5xl">Musicians DB</h1>
        <div class="text-right">
            <h2 class="text-xl pb-1 px-5 sm:p-0 sm:mr-12">Edit Musician</h2>
        </div>
    </div>

    

    <form action="/update_musician/{{ $musician->id }}?page={{ $page }}" method="post">@csrf
    <div class="px-1 sm:px-36">

        <div class="grid grid-cols-1 sm:grid-cols-2 sm:grid-rows-2 bg-zinc-300 px-8 py-6 sm:pt-6 sm:pb-0">
            <div class="order-1 sm:order-1 mb-6 sm:mb-0">
                <input type="text" name="first_name" value="{{ old('first_name') ?? $musician->first_name  }}" class="border-l-2 border-black p-1" placeholder="..first name" />
            </div>
            
            <div class="order-3 sm:order-2">
                <input type="text" name="last_name" value="{{ old('last_name') ?? $musician->last_name  }}" class="border-l-2 border-black p-1" placeholder="..last name" />
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
            Remove instruments:
        </div>

        <div class="bg-zinc-300 text-center sm:text-left p-3 remove_instrument_div">
            @foreach ($musician->instruments as $instrument)
                <button class="bg-red-900 text-white m-1 p-1 text-xs remove_instrument_btn" id="removebtn_{{ $instrument->pivot->instrument_id }}">{{ $instrument->name }} x</button>
            @endforeach
        </div>

        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Add instruments:
        </div>

        <div class="bg-zinc-300 text-center sm:text-left p-3">
            @foreach ($instruments as $instrument)
                <button class="bg-green-900 text-white m-1 p-1 text-xs add_instrument_btn" id="addbtn_{{ $instrument->id }}">{{ $instrument->name }} +</button>
            @endforeach
        </div>


        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Profile:
        </div> 


        <div class="p-3 bg-zinc-300">
            <textarea name="profile_text" class="w-full h-32 border-l border-black p-1 m-1">{{ $musician->profile_text ?? '' }}</textarea>
        </div>

        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            Details:
        </div> 


        <div class="p-3 bg-zinc-300">

            @for ($i = 0; $i < count($musician->musician_details_text); $i++)
                <div class="flex" id="musician_detail_{{ $musician->musician_details_id[$i] }}">
                    <div class="w-24">
                        {{ $musician->detail_types[$i] }}
                    </div>

                    <div class="w-72">
                        {{ $musician->musician_details_text[$i] }}
                    </div>

                    <div class="w-12">
                        <button class="bg-red-500 text-white rounded-xl px-2 h-6 delete_detail my-2" id="{{ $musician->musician_details_id[$i] }}">X</button>
                    </div>
                </div>
            @endfor
        </div>

        <div class="px-4 py-2 bg-zinc-500 text-zinc-100 mt-3">
            New Details:
        </div> 


        <div class="p-3 bg-gray-300">
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
            <button type="submit" class="border-l-4 border-black  bg-zinc-500 text-white p-1">UPDATE</button> 
            <button onclick="location.href='/?page=<?= request('page') ?>'" class="border-l-4 border-black  bg-zinc-500 text-white p-1">CANCEL</button>
        </div>


    </div>
    </form>


    

    



    <script>

        // de-associates a musician with an instrument via ajax
        $('.remove_instrument_btn').click(function() {

            let instrument_id = this.id.replace('removebtn_', '');
            event.preventDefault();

           $.post("/remove_musician_instrument", {
                _token: "{{ csrf_token() }}",
                musician_id: {{ $musician->id }},
                instrument_id: instrument_id
            });
            
            $('#removebtn_' + instrument_id).addClass('hidden');
        });
    

        // associates a musician with an instrument via ajax
        $('.add_instrument_btn').click(function() {

            let instrument_id = this.id.replace('addbtn_', '');
            event.preventDefault();

            // add the link in the database
            $.post("/add_musician_instrument", {
                _token: "{{ csrf_token() }}",
                musician_id: {{ $musician->id }},
                instrument_id: instrument_id
            });

            let instrument_name = $('#addbtn_' + instrument_id).html().replace(' +', '');
            $('.remove_instrument_div').append('<span class="text-white bg-black rounded m-1 p-1 text-xs nowrap">' + instrument_name + '</span>');
            $('#addbtn_' + instrument_id).addClass('hidden');
        });


        // deletes a detail when button is clicked
        $('.delete_detail').click(function() {

            event.preventDefault();

            $.post("/delete_musician_detail", {
                _token: "{{ csrf_token() }}",
                musician_detail_id: this.id
            });

            $('#musician_detail_' + this.id).addClass('hidden');


        });


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





