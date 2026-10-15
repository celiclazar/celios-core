@php
    $sectionId = $data['section_id'] ?? null;
    $sectionModel = $sectionId ? \Celios\Core\Models\Section::find($sectionId) : null;
@endphp

@if($sectionModel && $sectionModel->is_active)
    @include('components.sections.section', ['section' => $sectionModel])
@endif
