 <div class="form-group mb-20">
     <input type="text" class="form-control" name="name" placeholder="{{ $keywords['Name'] ?? __('Name') }}*" required
         value="{{ old('name') }}">
     @error('name')
         <p class=" text-danger">{{ $message }}</p>
     @enderror
 </div>
 <div class="form-group mb-20">
     <input type="email" class="form-control" required name="email"
         placeholder="{{ $keywords['Email Address'] ?? __('Email Address') }}*" value="{{ old('email') }}">
     @error('email')
         <p class=" text-danger">{{ $message }}</p>
     @enderror
 </div>
 <div class="form-group mb-20">
     <input type="number" class="form-control" name="phone" required value="{{ old('phone') }}"
         placeholder="{{ $keywords['Phone Number'] ?? __('Phone Number') }}*">
     @error('phone')
         <p class=" text-danger">{{ $message }}</p>
     @enderror
 </div>
 <div class="form-group mb-20">
     <textarea name="message" id="message" class="form-control" cols="30" rows="8" required=""
         data-error="Please enter your message" placeholder="{{ $keywords['Message'] ?? __('Message') }}...">{{ old('message') }}</textarea>

     @error('message')
         <p class=" text-danger">{{ $message }}</p>
     @enderror
 </div>
 @if ($basicInfo->google_recaptcha_status == 1)
     <div class="col-md-12">
         <div class="form-group mb-20">
             {!! NoCaptcha::renderJs() !!}
             {!! NoCaptcha::display() !!}
             @error('g-recaptcha-response')
                 <div class="help-block with-errors text-danger">{{ $message }}</div>
             @enderror
         </div>
     </div>
 @endif
 <button type="submit"
     class="btn btn-md btn-primary w-100">{{ $keywords['Send message'] ?? __('Send message') }}</button>
