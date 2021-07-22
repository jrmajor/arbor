<div class="flex flex-col">
  <label for="id_pytlewski" class="w-full font-medium pb-1 text-gray-700">
    {!! __('people.pytlewski.id') !!}
  </label>
  <div class="w-full flex">
    <input
      type="text" class="form-input rounded-r-none w-1/4 md:w-3/8 z-10 @error('id_pytlewski') invalid @enderror"
      id="id_pytlewski" name="id_pytlewski"
      autocomplete="off"
      wire:model="pytlewskiId">
    <input
      type="text" class="form-input rounded-l-none -ml-px w-3/4 md:w-5/8"
      wire:model="result"
      disabled>
  </div>
  @error('id_pytlewski')
    <div class="w-full leading-none mt-1">
      <small class="text-red-500">{{ $message }}</small>
    </div>
  @enderror
</div>
