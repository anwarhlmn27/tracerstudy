import re

file_path = r"c:\xampp\htdocs\tracers\resources\views\form.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Define the new switch block
new_switch = """                        @switch($question->question_type)
 
                             @case('text')
                                 <input type="text" name="answers[{{ $question->id }}]"
                                     value="{{ old('answers.' . $question->id) }}"
                                     class="w-full sm:w-3/4 py-3 px-4 border border-gray-300 rounded-xl focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-sm bg-white shadow-sm transition-all"
                                     placeholder="Ketik jawaban Anda..."
                                     @input="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>
                                 @break
 
                             @case('number')
                                 <input type="number" name="answers[{{ $question->id }}]"
                                     value="{{ old('answers.' . $question->id) }}"
                                     class="w-full sm:w-1/3 py-3 px-4 border border-gray-300 rounded-xl focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-sm bg-white shadow-sm transition-all"
                                     placeholder="0"
                                     @input="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>
                                 @break
 
                             @case('textarea')
                                 <textarea name="answers[{{ $question->id }}]" rows="3"
                                     class="w-full py-3 px-4 resize-y border border-gray-300 rounded-xl focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-sm bg-white shadow-sm transition-all"
                                     placeholder="Tulis jawaban Anda di sini..."
                                     @input="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>{{ old('answers.' . $question->id) }}</textarea>
                                 @break
 
                             @case('radio')
                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" x-data="{ selected: '{{ old('answers.' . $question->id, '') }}' }">
                                     @foreach($question->options as $option)
                                     <div class="option-card" :class="{ 'selected': selected === '{{ addslashes($option->option_text) }}' }"
                                          @click="selected = '{{ addslashes($option->option_text) }}'; trackAnswer('{{ $question->id }}', selected)">
                                         <input type="radio" name="answers[{{ $question->id }}]"
                                                value="{{ $option->option_text }}" class="sr-only"
                                                x-ref="radio_{{ $question->id }}_{{ $loop->index }}"
                                                :checked="selected === '{{ addslashes($option->option_text) }}'"
                                                {{ $question->is_required ? 'required' : '' }}>
                                         <div class="option-radio"></div>
                                         <span class="option-text">{{ $option->option_text }}</span>
                                     </div>
                                     @endforeach
                                 </div>
                                 @break
 
                             @case('select')
                                 <select name="answers[{{ $question->id }}]"
                                     class="no-ts w-full sm:w-2/3 rounded-xl border border-gray-300 px-4 py-3 focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-gray-800 bg-white shadow-sm transition-all text-sm"
                                     @change="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>
                                     <option value="">— Pilih salah satu —</option>
                                     @foreach($question->options as $option)
                                         <option value="{{ $option->option_text }}" {{ old('answers.' . $question->id) == $option->option_text ? 'selected' : '' }}>
                                             {{ $option->option_text }}
                                         </option>
                                     @endforeach
                                 </select>
                                 @break
 
                             @case('checkbox')
                                 @php $oldCheckbox = old('answers.' . $question->id, []); @endphp
                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5"
                                      x-data="{ checked: {{ json_encode(is_array($oldCheckbox) ? $oldCheckbox : []) }} }">
                                     @foreach($question->options as $option)
                                     <div class="option-card"
                                          :class="{ 'selected': checked.includes('{{ addslashes($option->option_text) }}') }"
                                          @click="toggleCheck(checked, '{{ addslashes($option->option_text) }}'); trackAnswer('{{ $question->id }}', checked.length > 0 ? 'filled' : '')">
                                         <input type="checkbox" name="answers[{{ $question->id }}][]"
                                                value="{{ $option->option_text }}" class="sr-only"
                                                :checked="checked.includes('{{ addslashes($option->option_text) }}')">
                                         <div class="option-checkbox">
                                             <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                         </div>
                                         <span class="option-text">{{ $option->option_text }}</span>
                                     </div>
                                     @endforeach
                                 </div>
                                 @break

                             @case('file')
                                 <div class="space-y-2">
                                     <input type="file" name="answers[{{ $question->id }}]"
                                         class="w-full sm:w-2/3 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 bg-white shadow-sm transition-all"
                                         @change="trackAnswer('{{ $question->id }}', $event.target.value)"
                                         {{ $question->is_required ? 'required' : '' }}>
                                     <p class="text-xs text-gray-400">Pilih berkas dari komputer Anda.</p>
                                 </div>
                                 @break

                             @case('linear_scale')
                                 @php
                                     $minVal = (int) ($question->options->where('sort_order', 0)->first()->option_text ?? 1);
                                     $maxVal = (int) ($question->options->where('sort_order', 1)->first()->option_text ?? 5);
                                     $minLabel = $question->options->where('sort_order', 2)->first()->option_text ?? '';
                                     $maxLabel = $question->options->where('sort_order', 3)->first()->option_text ?? '';
                                 @endphp
                                 <div class="flex items-center flex-wrap gap-4 py-2" x-data="{ val: '{{ old('answers.' . $question->id, '') }}' }">
                                     @if($minLabel)
                                         <span class="text-sm font-medium text-gray-500">{{ $minLabel }}</span>
                                     @endif
                                     <div class="flex items-center gap-1 sm:gap-2">
                                         @for($i = $minVal; $i <= $maxVal; $i++)
                                             <label class="flex flex-col items-center gap-2 cursor-pointer group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                                                 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}"
                                                     class="w-5 h-5 text-[#800000] border-gray-300 focus:ring-[#800000]"
                                                     x-model="val"
                                                     @change="trackAnswer('{{ $question->id }}', val)"
                                                     {{ $question->is_required ? 'required' : '' }}>
                                                 <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900">{{ $i }}</span>
                                             </label>
                                         @endfor
                                     </div>
                                     @if($maxLabel)
                                         <span class="text-sm font-medium text-gray-500">{{ $maxLabel }}</span>
                                     @endif
                                 </div>
                                 @break

                             @case('rating')
                                 @php
                                     $maxStars = (int) ($question->options->where('sort_order', 0)->first()->option_text ?? 5);
                                 @endphp
                                 <div class="flex items-center gap-1.5 py-1" x-data="{ 
                                     rating: {{ old('answers.' . $question->id) ?: 0 }}, 
                                     hoverRating: 0 
                                 }">
                                     <input type="hidden" name="answers[{{ $question->id }}]" :value="rating" {{ $question->is_required ? 'required' : '' }}>
                                     <template x-for="star in {{ $maxStars }}" :key="star">
                                         <button type="button" 
                                             @click="rating = star; trackAnswer('{{ $question->id }}', rating)"
                                             @mouseenter="hoverRating = star"
                                             @mouseleave="hoverRating = 0"
                                             class="focus:outline-none transition-transform duration-100 hover:scale-110">
                                             <svg class="w-8 h-8 transition-colors duration-150"
                                                 :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-amber-400 fill-amber-400' : 'text-gray-300 fill-none'"
                                                 stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                 <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                             </svg>
                                         </button>
                                     </template>
                                 </div>
                                 @break

                             @case('date')
                                 <input type="date" name="answers[{{ $question->id }}]"
                                     value="{{ old('answers.' . $question->id) }}"
                                     class="w-full sm:w-1/3 py-3 px-4 border border-gray-300 rounded-xl focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-sm bg-white shadow-sm transition-all text-gray-800"
                                     @input="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>
                                 @break

                             @case('time')
                                 <input type="time" name="answers[{{ $question->id }}]"
                                     value="{{ old('answers.' . $question->id) }}"
                                     class="w-full sm:w-1/4 py-3 px-4 border border-gray-300 rounded-xl focus:border-[#800000] focus:ring-4 focus:ring-[#800000]/10 text-sm bg-white shadow-sm transition-all text-gray-800"
                                     @input="trackAnswer('{{ $question->id }}', $event.target.value)"
                                     {{ $question->is_required ? 'required' : '' }}>
                                 @break"""

# We match from @switch to @endswitch
pattern = r"(\s*)@switch\(\$question->question_type\).*?@endswitch"
modified_content, count = re.subn(pattern, new_switch, content, flags=re.DOTALL)

if count > 0:
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(modified_content)
    print(f"Successfully replaced switch block in {file_path}")
else:
    print("Could not find the switch block pattern in the file.")
