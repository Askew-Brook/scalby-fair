<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($entries as $entry)
    <url>
        <loc>{{ url($entry->url()) }}</loc>
        <lastmod>{{ ($entry->lastModified() ?? now())->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
