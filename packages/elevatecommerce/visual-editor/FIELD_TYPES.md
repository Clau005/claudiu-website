# Section Field Types Reference

Complete guide to all available field types in section schemas.

## Basic Fields

### 1. Text
Single-line text input.

```json
{
  "title": {
    "type": "text",
    "label": "Title",
    "default": "Welcome",
    "required": true,
    "maxlength": 100
  }
}
```

### 2. Textarea
Multi-line text input.

```json
{
  "description": {
    "type": "textarea",
    "label": "Description",
    "rows": 5,
    "maxlength": 500
  }
}
```

### 3. Number
Number input with min/max.

```json
{
  "count": {
    "type": "number",
    "label": "Item Count",
    "min": 1,
    "max": 100,
    "default": 10
  }
}
```

## Boolean Fields

### 4. Boolean (Checkbox)
Simple checkbox.

```json
{
  "show_title": {
    "type": "boolean",
    "label": "Show Title",
    "default": true
  }
}
```

### 5. Switch (Toggle)
Modern toggle switch.

```json
{
  "enable_feature": {
    "type": "switch",
    "label": "Enable Feature",
    "default": false
  }
}
```

## Selection Fields

### 6. Select (Dropdown)
Dropdown menu.

```json
{
  "layout": {
    "type": "select",
    "label": "Layout",
    "options": {
      "grid": "Grid View",
      "list": "List View",
      "carousel": "Carousel"
    },
    "default": "grid"
  }
}
```

### 7. Radio
Radio button group.

```json
{
  "alignment": {
    "type": "radio",
    "label": "Text Alignment",
    "options": {
      "left": "Left",
      "center": "Center",
      "right": "Right"
    },
    "default": "center"
  }
}
```

## Visual Fields

### 8. Color
Color picker with hex input.

```json
{
  "background_color": {
    "type": "color",
    "label": "Background Color",
    "default": "#ffffff"
  }
}
```

### 9. Range (Slider)
Slider for numeric values.

```json
{
  "opacity": {
    "type": "range",
    "label": "Opacity",
    "min": 0,
    "max": 100,
    "default": 50
  }
}
```

## Link & Media Fields

### 10. URL
URL input with validation.

```json
{
  "link": {
    "type": "url",
    "label": "Link URL",
    "default": "https://example.com"
  }
}
```

### 11. Image
Image URL input (upload coming soon).

```json
{
  "background_image": {
    "type": "image",
    "label": "Background Image",
    "accept": "image/*"
  }
}
```

## Advanced Fields

### 12. Repeater
Repeating group of fields (for lists, items, etc.).

```json
{
  "features": {
    "type": "repeater",
    "label": "Feature",
    "fields": {
      "title": {
        "type": "text",
        "label": "Feature Title",
        "default": ""
      },
      "description": {
        "type": "textarea",
        "label": "Description",
        "default": ""
      },
      "icon": {
        "type": "text",
        "label": "Icon",
        "default": "⭐"
      }
    }
  }
}
```

## Complete Example

Here's a complete section config using multiple field types:

```json
{
  "label": "Feature Section",
  "category": "content",
  "icon": "✨",
  "schema": {
    "heading": {
      "type": "text",
      "label": "Section Heading",
      "default": "Our Features"
    },
    "subheading": {
      "type": "textarea",
      "label": "Subheading",
      "rows": 2
    },
    "layout": {
      "type": "select",
      "label": "Layout Style",
      "options": {
        "grid": "Grid",
        "list": "List"
      },
      "default": "grid"
    },
    "columns": {
      "type": "number",
      "label": "Columns",
      "min": 2,
      "max": 4,
      "default": 3
    },
    "show_icons": {
      "type": "switch",
      "label": "Show Icons",
      "default": true
    },
    "background_color": {
      "type": "color",
      "label": "Background Color",
      "default": "#f9fafb"
    },
    "text_align": {
      "type": "radio",
      "label": "Text Alignment",
      "options": {
        "left": "Left",
        "center": "Center",
        "right": "Right"
      },
      "default": "center"
    },
    "features": {
      "type": "repeater",
      "label": "Feature",
      "fields": {
        "title": {
          "type": "text",
          "label": "Title",
          "default": "Feature Title"
        },
        "description": {
          "type": "textarea",
          "label": "Description"
        },
        "link": {
          "type": "url",
          "label": "Learn More Link"
        }
      }
    }
  },
  "defaults": {
    "heading": "Our Features",
    "layout": "grid",
    "columns": 3,
    "show_icons": true,
    "background_color": "#f9fafb",
    "text_align": "center",
    "features": [
      {
        "title": "Fast Performance",
        "description": "Lightning fast load times",
        "link": ""
      },
      {
        "title": "Easy to Use",
        "description": "Intuitive interface",
        "link": ""
      }
    ]
  }
}
```

## Using in Blade Templates

Access field values using the `$settings` object:

```blade
<section style="background-color: {{ $settings->background_color }}">
    <div class="text-{{ $settings->text_align }}">
        <h2>{{ $settings->heading }}</h2>
        <p>{{ $settings->subheading }}</p>
        
        <div class="grid grid-cols-{{ $settings->columns }}">
            @foreach($settings->features as $feature)
                <div class="feature">
                    <h3>{{ $feature['title'] }}</h3>
                    <p>{{ $feature['description'] }}</p>
                    @if($feature['link'])
                        <a href="{{ $feature['link'] }}">Learn More</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
```

## Field Type Summary

| Type | Input | Use Case |
|------|-------|----------|
| `text` | Single line | Titles, names, short text |
| `textarea` | Multi-line | Descriptions, paragraphs |
| `number` | Number input | Counts, quantities |
| `boolean` | Checkbox | Simple yes/no |
| `switch` | Toggle | Enable/disable features |
| `select` | Dropdown | Choose from options |
| `radio` | Radio buttons | Mutually exclusive choices |
| `color` | Color picker | Colors, backgrounds |
| `range` | Slider | Opacity, size, spacing |
| `url` | URL input | Links, external resources |
| `image` | Image URL | Images, backgrounds |
| `repeater` | Nested fields | Lists, multiple items |

---

**All field types are now available in your page builder!** 🎉
