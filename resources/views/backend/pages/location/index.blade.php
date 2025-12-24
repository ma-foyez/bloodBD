<x-layouts.backend-layout>
    <x-slot name="breadcrumbsData">
        {{-- Breadcrumbs if needed --}}
    </x-slot>

    <div x-data="{ activeTab: 'division' }">
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'division'"
                        :class="{ 'border-blue-600 text-blue-600': activeTab === 'division', 'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'division' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab"
                        aria-controls="division" aria-selected="false">Divisions</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'district'"
                        :class="{ 'border-blue-600 text-blue-600': activeTab === 'district', 'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'district' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab"
                        aria-controls="district" aria-selected="false">Districts</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'area'"
                        :class="{ 'border-blue-600 text-blue-600': activeTab === 'area', 'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'area' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab"
                        aria-controls="area" aria-selected="false">Areas</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'union'"
                        :class="{ 'border-blue-600 text-blue-600': activeTab === 'union', 'border-transparent hover:text-gray-600 hover:border-gray-300': activeTab !== 'union' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab"
                        aria-controls="union" aria-selected="false">Unions</button>
                </li>
            </ul>
        </div>
        <div id="myTabContent">
            <div x-show="activeTab === 'division'" class="p-4 rounded-lg bg-gray-50" role="tabpanel"
                aria-labelledby="division-tab">
                @livewire('backend.location.division-manager')
            </div>
            <div x-show="activeTab === 'district'" class="p-4 rounded-lg bg-gray-50" role="tabpanel"
                aria-labelledby="district-tab" style="display: none;">
                @livewire('backend.location.district-manager')
            </div>
            <div x-show="activeTab === 'area'" class="p-4 rounded-lg bg-gray-50" role="tabpanel"
                aria-labelledby="area-tab" style="display: none;">
                @livewire('backend.location.area-manager')
            </div>
            <div x-show="activeTab === 'union'" class="p-4 rounded-lg bg-gray-50" role="tabpanel"
                aria-labelledby="union-tab" style="display: none;">
                @livewire('backend.location.union-manager')
            </div>
        </div>
    </div>

</x-layouts.backend-layout>
