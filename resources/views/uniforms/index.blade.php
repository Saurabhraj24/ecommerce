@extends('layouts.app')

@section('title', 'Amritsar School Uniforms - Bombay Fashion')
@section('meta_description', 'Official school uniforms for top Amritsar schools. Select school, class, and size with easy home delivery or store pickup.')

@section('content')
<div class="py-10 bg-festive-ivory/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Hero Header --}}
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-semibold bg-festive-blush text-festive-rose mb-3">
                Amritsar School Uniform Department
            </span>
            <h1 class="text-3xl sm:text-4xl font-serif font-bold text-festive-plum mb-3">
                Official School Uniforms
            </h1>
            <p class="text-sm text-festive-plum/70">
                Bombay Fashion is the trusted local supplier for Amritsar's premier schools. Select your child's school, grade, and gender to find matching approved uniform sets.
            </p>
        </div>

        {{-- Filter Wizard --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md mb-12">
            <form method="GET" action="{{ route('uniforms.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                {{-- School Select --}}
                <div>
                    <label for="school" class="block text-xs font-bold uppercase tracking-wider text-festive-plum mb-1.5">
                        1. Select School
                    </label>
                    <select 
                        name="school" 
                        id="school" 
                        onchange="this.form.submit()"
                        class="w-full text-xs font-medium rounded-xl border-festive-plum/20 bg-white/70 p-3 text-festive-plum focus:ring-festive-rose"
                    >
                        <option value="">-- Choose School --</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch }}" {{ $selectedSchool == $sch ? 'selected' : '' }}>
                                {{ $sch }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class / Grade --}}
                <div>
                    <label for="class" class="block text-xs font-bold uppercase tracking-wider text-festive-plum mb-1.5">
                        2. Select Class / Grade
                    </label>
                    <select 
                        name="class" 
                        id="class" 
                        onchange="this.form.submit()"
                        class="w-full text-xs font-medium rounded-xl border-festive-plum/20 bg-white/70 p-3 text-festive-plum focus:ring-festive-rose"
                        {{ empty($classes) ? 'disabled' : '' }}
                    >
                        <option value="">-- All Classes --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls }}" {{ $selectedClass == $cls ? 'selected' : '' }}>
                                Class {{ $cls }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender / Category --}}
                <div>
                    <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-festive-plum mb-1.5">
                        3. Gender
                    </label>
                    <select 
                        name="gender" 
                        id="gender" 
                        onchange="this.form.submit()"
                        class="w-full text-xs font-medium rounded-xl border-festive-plum/20 bg-white/70 p-3 text-festive-plum focus:ring-festive-rose"
                    >
                        <option value="">All (Boys & Girls)</option>
                        <option value="boys" {{ $selectedGender == 'boys' ? 'selected' : '' }}>Boys</option>
                        <option value="girls" {{ $selectedGender == 'girls' ? 'selected' : '' }}>Girls</option>
                        <option value="unisex" {{ $selectedGender == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                </div>

                {{-- Action / Reset --}}
                <div class="flex gap-2">
                    <button 
                        type="submit" 
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-xs bg-festive-rose text-white shadow-md hover:bg-festive-rose/90 transition-all text-center"
                    >
                        Find Uniforms
                    </button>
                    @if($selectedSchool || $selectedClass || $selectedGender)
                        <a 
                            href="{{ route('uniforms.index') }}" 
                            class="py-3 px-4 rounded-xl font-medium text-xs border border-festive-plum/20 text-festive-plum hover:bg-festive-blush transition-all text-center"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Uniform Results --}}
        <div>
            @if($uniformProducts->isEmpty())
                {{-- Clean Empty State - STRICT NO FAKE DATA --}}
                <div class="glass-card rounded-3xl p-12 text-center border border-white/80 bg-white/70 max-w-lg mx-auto">
                    <div class="w-16 h-16 rounded-full bg-festive-lavender/60 mx-auto flex items-center justify-center text-festive-plum mb-4">
                        <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-festive-plum mb-2">No Uniform Items Found</h3>
                    <p class="text-sm text-festive-plum/70 mb-4">
                        @if($selectedSchool)
                            We couldn't find items matching your criteria for "{{ $selectedSchool }}". Please visit our shop on Mahila College Road or contact us for custom tailoring.
                        @else
                            Select a school above to view certified uniform skirts, shirts, blazers, and ties.
                        @endif
                    </p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-festive-rose text-white text-xs font-bold shadow hover:bg-festive-rose/90 transition-all">
                        Contact Store Desk
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($uniformProducts as $product)
                        @include('products.partials.card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $uniformProducts->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
