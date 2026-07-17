---
description: "Use when editing CSS in this project. Covers responsive behavior at 1024px and below, boxed layouts on screens wider than 2k, section-level spacing, and stylesheet organization by page and section."
name: "CSS Organization Rules"
applyTo: "**/*.css"
---
# CSS Organization Rules

- Make layouts responsive at `1024px` and below. Treat this breakpoint as the main responsive checkpoint unless a section clearly needs an additional narrower breakpoint.
- Make sure the project does not visually extend beyond very wide screens. On screens wider than `2560px`, keep the site in a boxed layout by constraining content width with the existing container pattern.
- Avoid applying horizontal page padding at row level. Prefer section-level spacing or a dedicated container, following the current project structure.
- Organize the stylesheet by page and then by section.
- Add a large appendix at the top of the CSS file that lists each page section and its starting line.
- Order the stylesheet like this: Global Styles, Header, Navigation, Footer, then page-specific sections.
- Place media queries for a specific class or section immediately after its base declarations instead of collecting breakpoints elsewhere in the file.
- Preserve existing styles unless reorganization or responsive fixes are explicitly requested.