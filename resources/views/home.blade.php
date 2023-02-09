@extends('layout')

@section('content')

    <div class="border-y border-zinc-800 w-full px-1 py-5 sm:mt-10 sm:py-12 text-center">
        <h1 class="text-5xl sm:text-7xl text-zinc-900">Musicians DB</h1>
    </div>


    <div class="flex items-stretch w-full px-1 sm:px-36">

        <div class="self-end w-1/6 mt-5">
            <a href="/add_musician" class="border-l-4 border-black bg-zinc-500 text-zinc-100 p-2 text-xs whitespace-nowrap">ADD MUSICIAN</a>
        </div>

        <div class="w-5/6 text-right self-start{{ $musicians->lastPage() === 1 ? ' hidden sm:invisible' : '' }}">
            <h2 class="text-2xl mr-3 sm:mr-0">
                Page #{{ $musicians->currentPage() }}
            </h2>
        </div>
    </div>


    <div class="mt-6 border-b border-zinc-800 px-1 sm:px-36 pb-3">
        <form action="/" method="get">


            <div class="flex flex-row mx-0.5 sm:mx-0 sm:mb-1">
                <div class="order-2 sm:order-1 basis-1/2 p-1 sm:mx-0.5">
                    <input type="hidden" id="instruments" name="instruments" value="{{ request('instruments') }}">
                    <div class="border-l border-black px-1 h-36 overflow-y-scroll">
                        @foreach ($instruments as $instrument)
                            <div class="instrument{{ in_array($instrument->name, $instruments_filter) ? ' bg-zinc-500 text-white' : '' }}" id="{{ str_replace(' ', '_', $instrument->name) }}">{{ $instrument->name }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="order-1 sm:order-2 flex flex-wrap basis-1/2 grow sm:mx-0.5">
                    <div class="flex basis-full p-1">
                        <input type="text" name="name" value="{{ request('name') }}" class="border-l border-black block w-full my-auto m-0 sm:mx-4 p-1" placeholder="..search by name">
                    </div>
                    <label class="flex w-fit basis-full p-1">
                        <div class="m-auto text-sm">Only show musicians with profiles? <input type="checkbox" name="profile_only"{{ request('profile_only') ? ' checked' : '' }}></div>
                    </label>
                </div>

            </div>


            <div class="grid grid-cols-2 grid-rows-2">
                <div class="row-span-2 p-1 mx-0.5 text-xs text-black" id="instruments-box"></div>

                <div class="flex">
                    <button type="submit" class="border-l-4 border-black  bg-zinc-500 grow text-white m-0.5 p-1">SEARCH</button></div>
                
                <div>
                    <a href="/" class="w-full">
                        <div class="border-l-4 border-black  bg-zinc-500 text-white text-center m-0.5 p-1">
                            RESET
                        </div>
                    </a>
                </div>
            </div>
        </form> 
    </div>


    <div class="mt-6 border-b border-black px-1 sm:px-36 pb-3">

        <a href="/pdf?{{ request('instruments') ? 'instruments=' . request('instruments') : '' }}{{ request('name') ? '&name=' . request('name') : '' }}" class="{{ count($musicians) > 0 ? 'inline-block ' : 'hidden ' }} border-l-4 border-black  bg-zinc-500 text-white text-center mb-5 p-1">Generate PDF from results</a>

        @forelse ($musicians as $musician)

        <div class="bg-zinc-300 rounded pb-2 flex flex-row flex-wrap mb-1">

            <div class="ml-4 sm:ml-12 w-full">
                {{ $musician->first_name }} {{ $musician->last_name }}
                @if ($musician->profile_id)
                    <button class="text-xs ml-2 profile" id="profile_{{ $musician->profile_id }}">[show profile]</button>
                    <br>
                    <div class="hidden bg-gray-200 text-sm p-2 m-2 profile_box" id="profile_box_{{ $musician->profile_id }}"></div>
                @endif
            </div>

            <div class="ml-7 sm:ml-16 basis-full sm:basis-2/6">
                @for ($i = 0; $i < count($musician->musician_details_text); $i++)
                    @if ($musician->detail_types[$i] === 'website')
                        <a href="{{ $musician->musician_details_text[$i] }}" target="_blank">[website]</a>
                    @elseif ($musician->detail_types[$i] === 'facebook')
                        <a href="{{ $musician->musician_details_text[$i] }}" class="inline" target="_blank"><img class="inline" alt="Facebook Page" width="25" height="25" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iNDgiIGhlaWdodD0iNDgiCnZpZXdCb3g9IjAgMCA0OCA0OCI+CjxsaW5lYXJHcmFkaWVudCBpZD0iYXdTZ0lpbmZ3NV9GUzVNTEhJfkE5YV95R2NXTDhjb3BOTlFfZ3IxIiB4MT0iNi4yMjgiIHgyPSI0Mi4wNzciIHkxPSI0Ljg5NiIgeTI9IjQzLjQzMiIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzBkNjFhOSI+PC9zdG9wPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzE2NTI4YyI+PC9zdG9wPjwvbGluZWFyR3JhZGllbnQ+PHBhdGggZmlsbD0idXJsKCNhd1NnSWluZnc1X0ZTNU1MSEl+QTlhX3lHY1dMOGNvcE5OUV9ncjEpIiBkPSJNNDIsNDBjMCwxLjEwNS0wLjg5NSwyLTIsMkg4Yy0xLjEwNSwwLTItMC44OTUtMi0yVjhjMC0xLjEwNSwwLjg5NS0yLDItMmgzMgljMS4xMDUsMCwyLDAuODk1LDIsMlY0MHoiPjwvcGF0aD48cGF0aCBkPSJNMjUsMzhWMjdoLTR2LTZoNHYtMi4xMzhjMC01LjA0MiwyLjY2Ni03LjgxOCw3LjUwNS03LjgxOGMxLjk5NSwwLDMuMDc3LDAuMTQsMy41OTgsMC4yMDgJbDAuODU4LDAuMTExTDM3LDEyLjIyNEwzNywxN2gtMy42MzVDMzIuMjM3LDE3LDMyLDE4LjM3OCwzMiwxOS41MzVWMjFoNC43MjNsLTAuOTI4LDZIMzJ2MTFIMjV6IiBvcGFjaXR5PSIuMDUiPjwvcGF0aD48cGF0aCBkPSJNMjUuNSwzNy41di0xMWgtNHYtNWg0di0yLjYzOGMwLTQuNzg4LDIuNDIyLTcuMzE4LDcuMDA1LTcuMzE4YzEuOTcxLDAsMy4wMywwLjEzOCwzLjU0LDAuMjA0CWwwLjQzNiwwLjA1N2wwLjAyLDAuNDQyVjE2LjVoLTMuMTM1Yy0xLjYyMywwLTEuODY1LDEuOTAxLTEuODY1LDMuMDM1VjIxLjVoNC42NGwtMC43NzMsNUgzMS41djExSDI1LjV6IiBvcGFjaXR5PSIuMDciPjwvcGF0aD48cGF0aCBmaWxsPSIjZmZmIiBkPSJNMzMuMzY1LDE2SDM2di0zLjc1NGMtMC40OTItMC4wNjQtMS41MzEtMC4yMDMtMy40OTUtMC4yMDNjLTQuMTAxLDAtNi41MDUsMi4wOC02LjUwNSw2LjgxOVYyMmgtNHY0CWg0djExaDVWMjZoMy45MzhsMC42MTgtNEgzMXYtMi40NjVDMzEsMTcuNjYxLDMxLjYxMiwxNiwzMy4zNjUsMTZ6Ij48L3BhdGg+Cjwvc3ZnPg=="/></a>
                    @elseif ($musician->detail_types[$i] === 'linkedin')
                        <a href="{{ $musician->musician_details_text[$i] }}" target="_blank"><img class="inline" alt="LinkedIn Page" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjUiIGhlaWdodD0iMjUiCnZpZXdCb3g9IjAgMCAzMCAzMCI+CiAgICA8cGF0aCBkPSJNMTUsM0M4LjM3MywzLDMsOC4zNzMsMywxNWMwLDYuNjI3LDUuMzczLDEyLDEyLDEyczEyLTUuMzczLDEyLTEyQzI3LDguMzczLDIxLjYyNywzLDE1LDN6IE0xMC40OTYsOC40MDMgYzAuODQyLDAsMS40MDMsMC41NjEsMS40MDMsMS4zMDljMCwwLjc0OC0wLjU2MSwxLjMwOS0xLjQ5NiwxLjMwOUM5LjU2MSwxMS4wMjIsOSwxMC40Niw5LDkuNzEyQzksOC45NjQsOS41NjEsOC40MDMsMTAuNDk2LDguNDAzeiBNMTIsMjBIOXYtOGgzVjIweiBNMjIsMjBoLTIuODI0di00LjM3MmMwLTEuMjA5LTAuNzUzLTEuNDg4LTEuMDM1LTEuNDg4cy0xLjIyNCwwLjE4Ni0xLjIyNCwxLjQ4OGMwLDAuMTg2LDAsNC4zNzIsMCw0LjM3MkgxNHYtOCBoMi45MTh2MS4xMTZDMTcuMjk0LDEyLjQ2NSwxOC4wNDcsMTIsMTkuNDU5LDEyQzIwLjg3MSwxMiwyMiwxMy4xMTYsMjIsMTUuNjI4VjIweiI+PC9wYXRoPgo8L3N2Zz4="/></a>
                    @elseif ($musician->detail_types[$i] === 'myspace')
                        <a href="{{ $musician->musician_details_text[$i] }}" target="_blank"><img class="inline" alt="Myspace Page" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjUiIGhlaWdodD0iMjUiCnZpZXdCb3g9IjAgMCA0OCA0OCI+CjxsaW5lYXJHcmFkaWVudCBpZD0iMzB4S3JEeGdrN1FYdUlTMkVTQmgzYV9mb2VRdmpIeEFiR0xfZ3IxIiB4MT0iNi44MTEiIHgyPSIxMy4xOTgiIHkxPSI5LjAxNCIgeTI9IjMzLjQ1OCIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzU5NjFjMyI+PC9zdG9wPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzNhNDFhYyI+PC9zdG9wPjwvbGluZWFyR3JhZGllbnQ+PHBhdGggZmlsbD0idXJsKCMzMHhLckR4Z2s3UVh1SVMyRVNCaDNhX2ZvZVF2akh4QWJHTF9ncjEpIiBkPSJNMTEuNSwxN0M4LjQ2MywxNyw2LDE5LjQ2Miw2LDIyLjVWMzJjMCwwLjU1MiwwLjQ0OCwxLDEsMWg5YzAuNTUyLDAsMS0wLjQ0OCwxLTF2LTkuNQlDMTcsMTkuNDYyLDE0LjUzNywxNywxMS41LDE3eiI+PC9wYXRoPjxsaW5lYXJHcmFkaWVudCBpZD0iMzB4S3JEeGdrN1FYdUlTMkVTQmgzYl9mb2VRdmpIeEFiR0xfZ3IyIiB4MT0iMTAuNTkyIiB4Mj0iMTYuOTgiIHkxPSI4LjAyNiIgeTI9IjMyLjQ3IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjNTk2MWMzIj48L3N0b3A+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM2E0MWFjIj48L3N0b3A+PC9saW5lYXJHcmFkaWVudD48Y2lyY2xlIGN4PSIxMS41IiBjeT0iMTEuNSIgcj0iMy41IiBmaWxsPSJ1cmwoIzMweEtyRHhnazdRWHVJUzJFU0JoM2JfZm9lUXZqSHhBYkdMX2dyMikiPjwvY2lyY2xlPjxsaW5lYXJHcmFkaWVudCBpZD0iMzB4S3JEeGdrN1FYdUlTMkVTQmgzY19mb2VRdmpIeEFiR0xfZ3IzIiB4MT0iMTQuODAzIiB4Mj0iMjMuMjM1IiB5MT0iNy43OTgiIHkyPSIzNi44NDEiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj48c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiM1OTYxYzMiPjwvc3RvcD48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMzYTQxYWMiPjwvc3RvcD48L2xpbmVhckdyYWRpZW50PjxwYXRoIGZpbGw9InVybCgjMzB4S3JEeGdrN1FYdUlTMkVTQmgzY19mb2VRdmpIeEFiR0xfZ3IzKSIgZD0iTTIxLDE4Yy0zLjMxNCwwLTYsMi42ODctNiw2djExYzAsMC41NTIsMC40NDgsMSwxLDFoMTBjMC41NTIsMCwxLTAuNDQ4LDEtMVYyNAlDMjcsMjAuNjg3LDI0LjMxNCwxOCwyMSwxOHoiPjwvcGF0aD48bGluZWFyR3JhZGllbnQgaWQ9IjMweEtyRHhnazdRWHVJUzJFU0JoM2RfZm9lUXZqSHhBYkdMX2dyNCIgeDE9IjE5LjY2MSIgeDI9IjI4LjA5MyIgeTE9IjYuMzg3IiB5Mj0iMzUuNDMxIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjNTk2MWMzIj48L3N0b3A+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM2E0MWFjIj48L3N0b3A+PC9saW5lYXJHcmFkaWVudD48Y2lyY2xlIGN4PSIyMSIgY3k9IjExIiByPSI0IiBmaWxsPSJ1cmwoIzMweEtyRHhnazdRWHVJUzJFU0JoM2RfZm9lUXZqSHhBYkdMX2dyNCkiPjwvY2lyY2xlPjxsaW5lYXJHcmFkaWVudCBpZD0iMzB4S3JEeGdrN1FYdUlTMkVTQmgzZV9mb2VRdmpIeEFiR0xfZ3I1IiB4MT0iMjMuNTQ2IiB4Mj0iMzYuMTUxIiB5MT0iNi42NzMiIHkyPSI0NC4yMzMiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj48c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiM1OTYxYzMiPjwvc3RvcD48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMzYTQxYWMiPjwvc3RvcD48L2xpbmVhckdyYWRpZW50PjxwYXRoIGZpbGw9InVybCgjMzB4S3JEeGdrN1FYdUlTMkVTQmgzZV9mb2VRdmpIeEFiR0xfZ3I1KSIgZD0iTTMzLDIwYy00Ljk3LDAtOSw0LjAzLTksOXYxM2MwLDAuNTUyLDAuNDQ4LDEsMSwxaDE2YzAuNTUyLDAsMS0wLjQ0OCwxLTFWMjkJQzQyLDI0LjAzLDM3Ljk3LDIwLDMzLDIweiI+PC9wYXRoPjxsaW5lYXJHcmFkaWVudCBpZD0iMzB4S3JEeGdrN1FYdUlTMkVTQmgzZl9mb2VRdmpIeEFiR0xfZ3I2IiB4MT0iMzAuNzM4IiB4Mj0iNDMuMzQzIiB5MT0iNC4yNTkiIHkyPSI0MS44MiIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzU5NjFjMyI+PC9zdG9wPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzNhNDFhYyI+PC9zdG9wPjwvbGluZWFyR3JhZGllbnQ+PGNpcmNsZSBjeD0iMzMiIGN5PSIxMSIgcj0iNiIgZmlsbD0idXJsKCMzMHhLckR4Z2s3UVh1SVMyRVNCaDNmX2ZvZVF2akh4QWJHTF9ncjYpIj48L2NpcmNsZT4KPC9zdmc+"/></a>
                    @else
                        <span class="text-xs">{{ $musician->detail_types[$i] }}</span>: <span class="text-sm">{{ $musician->musician_details_text[$i] }}</span><br>
                    @endif

                @endfor
            </div>
            
            <div class="text-xs mb-2 w-full text-center sm:w-auto sm:basis-2/6">
                @foreach ($musician->instruments as $instrument)
                    <a href="/?instruments={{ str_replace(' ', '_', $instrument->name) }}" class="underline">{{ $instrument->name }}</a>@if (! $loop->last),@endif
                @endforeach
            </div>

            <div class="justify-around sm:justify-start w-full sm:w-min mb-2 sm:basis-1/6">
                <form action="/delete_musician/{{ $musician->id }}?page={{ $musicians->currentPage() }}" method="post" onsubmit="return confirm('are you sure?')">@csrf
                
                <a href="/edit_musician/{{ $musician->id }}?page={{ $musicians->currentPage() }}" class="border-l-4 border-black  bg-zinc-500 text-white px-1 h-6 mr-3 sm:mb-2">EDIT</a><br>
                
                <button type="submit" class="border-l-4 border-black  bg-zinc-500 text-white px-1 h-6 sm:mt-2">DELETE</button>
                </form>
            </div>
        </div>

        @empty

            <div class="justify-around mt-12 sm:mt-32">
                Sorry, no musicians found. Click <a href="/" class="text-blue-500 underline mx-1">here</a> to reset.
            </div>

    @endforelse


    </div>


    <div class="justify-around pb-8 px-6 pt-4 border-t border-black">{{ $musicians->links() }}</div>




    <script>



        // fill the instruments-box with instruments from the querystring
        $(document).ready(function(){
            querystring = '{{ request('instruments') }}';
            instruments_string = querystring.replaceAll('*', ', ');
            instruments_string = instruments_string.replaceAll('_', ' ');
            // if (instruments_string.substring(0,1) === ' ') instruments_string = instruments_string.substring(1);
            // instruments_string.substring(-1);
            $('#instruments-box').html(instruments_string);
        });



        // show/hide musicians' profiles
        $('.profile').click(function () {

            profile_id = this.id.replace('profile_', '');

            // show
            if ($('#' + this.id).html() === '[show profile]') {

                $('.profile_box').addClass('hidden');
                $('.profile').html('[show profile]');

                $.post('/get_profile/' + profile_id, {_token: "{{ csrf_token() }}" }, function(result){
                    $('#profile_box_' + profile_id).html(result);
                    $('#profile_box_' + profile_id).removeClass('hidden');
                });

                $('#' + this.id).html('[hide profile]');
            }
            // hide
            else {
                $('#profile_box_' + profile_id).html('');
                $('#profile_box_' + profile_id).addClass('hidden');
                $('#' + this.id).html('[show profile]');
            }

        });



        // select multiple instruments for filter
        $('.instrument').click(function() {

            // add an instrument to the hidden form and make the background different
            if (! $('#' + this.id).hasClass('bg-zinc-500')) {

                $('#' + this.id).addClass('bg-zinc-500');
                $('#' + this.id).addClass('text-white');
                instruments = $('#instruments').val().split('*');
                instruments.push(this.id);
                instruments_string = instruments.join('*');
                if (instruments_string.substring(0,1) === '*') instruments_string = instruments_string.substring(1);
                $('#instruments').val(instruments_string);
                instruments_string = instruments.join(', ');
                instruments_string = instruments_string.replaceAll('_', ' ');
                if (instruments_string.substring(0,2) === ', ') instruments_string = instruments_string.substring(2);
                if (instruments_string.substring(0,1) === ' ') instruments_string = instruments_string.substring(1);
                $('#instruments-box').html(instruments_string);                
            }
            // remove an instrument from the hidden form and remove the different background
            else {
                $('#' + this.id).removeClass('bg-zinc-500');
                $('#' + this.id).removeClass('text-white');
                instruments = $('#instruments').val().split('*');
                key = instruments.indexOf(this.id);
                if (key !== -1) instruments.splice(key, 1);
                instruments_string = instruments.join('*');
                if (instruments_string.substring(0,1) === '*') instruments_string = instruments_string.substring(1);
                $('#instruments').val(instruments_string);
                instruments_string = instruments.join(', ');
                instruments_string = instruments_string.replaceAll('_', ' ');
                if (instruments_string.substring(0,1) === ' ') instruments_string = instruments_string.substring(1);
                $('#instruments-box').html(instruments_string);


            }

        });

    </script>


@endsection



