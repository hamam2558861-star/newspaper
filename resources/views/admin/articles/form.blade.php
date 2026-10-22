@extends('layouts.admin')
@php
    $isEdit = $article->exists;
@endphp
@section('title', $isEdit ? 'تحرير مادة' : 'مادة جديدة')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
@endpush

@section('content')
@php
    $canPublish = auth()->user()->can('articles.publish');
    $mine = ! $isEdit || $article->user_id === auth()->id();
@endphp
<div class="page-head">
    <div>
        <h1>{{ $isEdit ? 'تحرير مادة' : 'كتابة مادة جديدة' }}</h1>
        @if($isEdit)<p>@include('admin.articles._status', ['a' => $article]) · الكاتب: {{ $article->author->name }} · آخر تعديل {{ $article->updated_at->diffForHumans() }}</p>@endif
    </div>
    @if($isEdit)<a class="btn btn-ghost" href="{{ $article->url() }}" target="_blank">معاينة / عرض ↗</a>@endif
</div>

@if($isEdit && $article->status === 'rejected' && $article->reject_reason)
    <div class="reject-box"><strong>سبب الرفض:</strong> {{ $article->reject_reason }}</div>
@endif

{{-- قرارات المراجعة لمن يملك صلاحية النشر --}}
@if($isEdit && $canPublish && in_array($article->status, ['pending', 'draft', 'rejected', 'published', 'archived']))
<div class="card" style="margin-bottom:20px">
    <div class="card-b" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <strong style="margin-inline-end:auto">إجراءات التحرير:</strong>
        @if(! $article->isLive())
        <form method="post" action="{{ route('admin.articles.workflow', $article) }}">@csrf<input type="hidden" name="decision" value="publish"><button class="btn btn-green btn-sm">✔ اعتماد ونشر الآن</button></form>
        @endif
        @if(in_array($article->status, ['pending', 'draft']))
        <button type="button" id="reject-toggle" class="btn btn-danger btn-sm">✖ رفض مع ملاحظات</button>
        @endif
        @if($article->status === 'published')
        <form method="post" action="{{ route('admin.articles.workflow', $article) }}">@csrf<input type="hidden" name="decision" value="draft"><button class="btn btn-ghost btn-sm">إلغاء النشر (مسودة)</button></form>
        <form method="post" action="{{ route('admin.articles.workflow', $article) }}">@csrf<input type="hidden" name="decision" value="archive"><button class="btn btn-ghost btn-sm">أرشفة</button></form>
        @endif
        <form method="post" action="{{ route('admin.articles.workflow', $article) }}" id="reject-box" style="display:none;width:100%">
            @csrf <input type="hidden" name="decision" value="reject">
            <div class="field" style="margin:10px 0 8px"><label>سبب الرفض (سيظهر للكاتب)</label><textarea name="reject_reason" rows="3" required></textarea></div>
            <button class="btn btn-red btn-sm">تأكيد الرفض</button>
        </form>
    </div>
</div>
@endif

<form method="post" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="editor-grid">
        <div>
            <div class="card">
                <div class="card-b">
                    <x-a.input name="kicker" label="العنوان الفوقي (اختياري)" :value="$article->kicker" hint="كلمة أو عبارة قصيرة تظهر فوق العنوان، مثل: عاجل، تحليل، تقرير خاص." />
                    <x-a.input name="title" label="العنوان" :value="$article->title" required maxlength="200" style="font-size:20px;font-weight:700" />
                    <x-a.textarea name="excerpt" label="المقدمة / الملخص" :value="$article->excerpt" rows="3" maxlength="500" id="excerpt" data-count="excerpt-c" hint="تظهر تحت العنوان وفي نتائج البحث ومشاركات التواصل. إن تركتها فارغة تُستخرج تلقائياً." />
                    <div class="hint" id="excerpt-c" style="margin-top:-12px;margin-bottom:14px"></div>

                    <div class="field">
                        <label>نص المادة <span style="color:var(--red)">*</span></label>
                        <div id="editor" data-upload="{{ route('admin.upload.image') }}"></div>
                        <input type="hidden" name="body" id="body-input" value="{{ old('body', $article->body) }}">
                        @error('body')<div class="err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px">
                <div class="card-h"><h3>تحسين محركات البحث (SEO)</h3></div>
                <div class="card-b">
                    <x-a.input name="meta_title" label="عنوان مخصص لمحركات البحث" :value="$article->meta_title" maxlength="160" />
                    <x-a.textarea name="meta_description" label="وصف مخصص لمحركات البحث" :value="$article->meta_description" rows="2" maxlength="300" />
                </div>
            </div>
        </div>

        <div class="side">
            <div class="card">
                <div class="card-h"><h3>التصنيف</h3></div>
                <div class="card-b">
                    <div class="field">
                        <label>القسم <span style="color:var(--red)">*</span></label>
                        <select name="category_id" required>
                            <option value="">— اختر القسم —</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $article->category_id) == $c->id)>{{ $c->parent_id ? '— ' : '' }}{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>نوع المادة</label>
                        <select name="type">@foreach(config('newsroom.article_types') as $k => $v)<option value="{{ $k }}" @selected(old('type', $article->type) === $k)>{{ $v }}</option>@endforeach</select>
                    </div>
                    <x-a.input name="tags" label="الوسوم" :value="$tagsText" hint="افصل بين الوسوم بفاصلة (،)" />
                    <x-a.input name="dateline" label="مكان الخبر" :value="$article->dateline" placeholder="مثال: عمّان" />
                    <x-a.input name="source" label="المصدر" :value="$article->source" />
                </div>
            </div>

            <div class="card">
                <div class="card-h"><h3>الوسائط</h3></div>
                <div class="card-b">
                    <img id="img-prev" class="img-preview" src="{{ $article->image_url }}" style="{{ $article->image_url ? '' : 'display:none' }}" alt="" onerror="this.style.display='none'">
                    <x-a.input name="image" label="الصورة الرئيسية" type="file" accept="image/*" data-preview="img-prev" hint="يُفضّل 1200×675 بكسل." />
                    <x-a.input name="image_caption" label="وصف الصورة" :value="$article->image_caption" />
                    @if($article->image)<x-a.check name="remove_image" label="حذف الصورة الحالية" />@endif
                    <x-a.input name="video_url" label="رابط فيديو يوتيوب" type="url" :value="$article->video_url" dir="ltr" placeholder="https://www.youtube.com/watch?v=..." />
                </div>
            </div>

            <div class="card">
                <div class="card-h"><h3>خيارات النشر</h3></div>
                <div class="card-b">
                    <x-a.check name="allow_comments" label="السماح بالتعليقات" :checked="(bool) $article->allow_comments" />
                    @if($canPublish)
                        <x-a.check name="is_breaking" label="خبر عاجل" hint="يظهر في الشريط العاجل أعلى الموقع" :checked="(bool) $article->is_breaking" />
                        <x-a.check name="is_featured" label="مادة مميّزة" hint="تظهر في واجهة الصفحة الرئيسية" :checked="(bool) $article->is_featured" />
                        <x-a.input name="published_at" label="موعد النشر (للجدولة)" type="datetime-local" :value="$article->published_at?->format('Y-m-d\TH:i')" hint="اتركه فارغاً للنشر الفوري. تاريخ مستقبلي = نشر مجدول." />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="sticky-actions">
            @if(! $isEdit || in_array($article->status, ['draft', 'rejected', 'pending']))
                <button class="btn btn-ghost" name="action" value="draft">حفظ كمسودة</button>
            @endif
            @if($mine && (! $isEdit || in_array($article->status, ['draft', 'rejected'])))
                <button class="btn" name="action" value="submit">إرسال للمراجعة</button>
            @endif
            @if($canPublish)
                <button class="btn btn-green" name="action" value="publish">{{ $article->isLive() ? 'حفظ ونشر التعديلات' : 'نشر / جدولة' }}</button>
            @endif
            @if($isEdit && ($article->isLive() || in_array($article->status, ['archived'])) && ! $canPublish)
                <button class="btn" type="submit">حفظ التعديلات</button>
            @endif
        </div>
    </div>
</form>
@endsection
