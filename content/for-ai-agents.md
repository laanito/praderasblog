---
Title: Para agentes y desarrolladores (API JSON)
Description: Punto de entrada público para consumir el blog sin HTML — endpoints JSON, versión de esquema, idiomas y etiquetas canónicas.
Lang: es
Translation_Key: praderas-for-ai-agents
Robots: index, follow
---

# Para agentes y desarrolladores

Esta página resume cómo **leer el blog Praderas de forma machine-friendly** sin parsear plantillas Twig ni barras laterales. El HTML sigue siendo la vista canónica para personas y SEO; JSON es una **representación paralela** servida por el mismo Pico en rutas dedicadas (`plugins/70-BlogJson.php`).

Contrato detallado en el repositorio: [`.agents/blog-json-api.md`](https://github.com/laanito/praderasblog/blob/main/.agents/blog-json-api.md) (si trabajas desde git). En producción, sustituye el host por `https://blog.praderas.org`.

## Esquema actual

- **`schema_version`:** `1.1`
- **Idiomas:** listados y búsqueda **puros por idioma** (`es` vs `en`); las etiquetas en JSON permanecen en **español canónico** (igual que el YAML `Tags`).

## Endpoints

| Método | Ruta | Uso |
|--------|------|-----|
| GET | `/blog.json` | Listado de artículos en español |
| GET | `/blog/en.json` | Listado en inglés |
| GET | `/blog/{slug}.json` | Artículo ES + cuerpo markdown |
| GET | `/blog/en/{slug}.json` | Artículo EN + cuerpo markdown |
| GET | `/search.json?q=…` | Búsqueda ES (mismo ranking que `/search/…`) |
| GET | `/en/search.json?q=…` | Búsqueda EN |

Cabeceras habituales: `Content-Type: application/json; charset=utf-8`, `Cache-Control: public, max-age=3600`.

## Campos útiles para RAG

En listados y resultados de búsqueda: `word_count`, `estimated_tokens` (aprox. `strlen/4`), `modified_at`, `translation_key`, `alternate_url`, `reading_time_minutes`.

En búsqueda JSON, cada ítem puede incluir `search_rank`.

## Ejemplos

```bash
curl -sS 'https://blog.praderas.org/blog.json' | head
curl -sS 'https://blog.praderas.org/search.json?q=traduccion' | jq '.meta'
curl -sS 'https://blog.praderas.org/blog/en/reviving-praderas-day-24-tier-a-days-8-9-and-search-json.json' | jq '.post.title'
```

## Reglas de contenido

1. **No mezclar idiomas** en un mismo feed JSON; usa `/blog.json` o `/blog/en.json`.
2. **Etiquetas:** nombres en español en JSON; etiquetas visibles en inglés salen de `scripts/tag_vocabulary.json` en HTML, no en estos endpoints.
3. **Pares de traducción:** mismo `translation_key` en ES/EN; `alternate_url` cuando existe par.

## Más contexto editorial

Serie *Reviviendo Praderas* (Días 23–25) documenta el despliegue de la API y el retrofit de portadas. Para política de redacción humana vs JSON, ver [guías editoriales en `.agents/`](https://github.com/laanito/praderasblog/tree/main/.agents).
