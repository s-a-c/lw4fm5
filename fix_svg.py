
import re

source_file = 'resources/views/pages/tailwindcss.catppuccin.html'
target_file = 'public/images/catppuccin/pepperjack-logo.svg'

with open(source_file, 'r') as f:
    lines = f.readlines()

# Extract SVG block (lines 47-177, which are 0-indexed 46-177)
# Wait, line numbers in view_file are 1-indexed.
# Line 47 is the <svg tag.
# Line 177 is the </svg> tag.
svg_lines = lines[46:177]

# Join them to a string
svg_content = "".join(svg_lines)

# Define the CSS to inject
extra_css = """
      /* Default (Latte) */
      :root {
        --catppuccin-color-text: #4c4f69;
        --catppuccin-color-red: #d20f39;
        color: #e6e9ef; /* Mantle */
      }

      @media (prefers-color-scheme: dark) {
        :root {
          /* Mocha */
          --catppuccin-color-text: #cdd6f4;
          --catppuccin-color-red: #f38ba8;
          color: #11111b; /* Crust */
        }
      }
"""

# Inject CSS into the existing :root block or after it.
# The existing :root block ends at line 63 (in original file).
# In the extracted string, we can look for "}" inside the <style> block.
# Or just replace ":root {" with ":root {" + extra_css_properties
# But I have media queries too.

# Let's just insert the extra CSS before the closing </style> tag.
# But wait, the variables need to be defined.
# The original :root block defines --logo-mauve etc.
# I can just append my new rules after the existing :root block.

# Find the closing brace of the first :root block?
# Or just add my new CSS at the end of the style block?
# CSS variables in :root are global.
# If I add another :root block, it should merge.
# But I also have the media query.

# So I will insert my extra CSS before </style>.
svg_content = svg_content.replace("</style>", extra_css + "\n    </style>")

# Also remove the data-astro-cid attributes to clean it up?
# The user didn't explicitly ask to remove them, but it's cleaner.
# Regex to remove data-astro-cid- attributes
svg_content = re.sub(r'\s+data-astro-cid-[a-z0-9]+', '', svg_content)
svg_content = re.sub(r'\s+data-astro-cid-[a-z0-9]+=""', '', svg_content) # In case of empty value

# Write to target file
with open(target_file, 'w') as f:
    f.write(svg_content)

print(f"Successfully wrote {target_file}")
