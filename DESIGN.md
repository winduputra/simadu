# SIMADU Design System

## 1. Product And Visual Direction

SIMADU is an operational document-management interface. Responsive work preserves
the existing desktop identity rather than redesigning it: quiet slate surfaces,
indigo emphasis, white bordered cards, restrained shadows, and compact data-rich
layouts. Mobile adaptations prioritize access, clarity, and predictable reflow.

## 2. Foundations And Tokens

- Primary accent: Tailwind `indigo-500`, `indigo-600`, and `indigo-700`.
- Application surfaces: `slate-50` page, `white` cards and header, `slate-900`
  navigation, and `slate-200` borders.
- Semantic feedback: emerald for success, rose for destructive/error states,
  and amber only where an existing warning state uses it.
- Corners: `rounded-lg` for compact controls, `rounded-xl` for cards and menus,
  and `rounded-2xl` for prominent panels already using that radius.
- Elevation: borders establish structure; `shadow-sm` and restrained menu/modal
  shadows provide hierarchy without decorative depth.

## 3. Typography

- Use Instrument Sans at the weights already loaded by the application.
- Preserve the current heading scale and body sizes on desktop.
- Long filenames, unit names, breadcrumbs, and metadata must use `min-w-0`,
  truncation, wrapping, or a local scroll region instead of widening the page.
- Truncation must not remove the accessible name or the ability to inspect the
  full value where it is operationally important.

## 4. Layout And Spacing

- Use Tailwind's existing spacing scale. Main content uses `p-4 sm:p-6 md:p-8`.
- Cards keep their existing composition; reduce padding only at narrow widths.
- Page headers stack below `sm` and return to horizontal alignment at `sm+`.
- Action groups may become full-width stacked controls on mobile, then return to
  intrinsic-width rows at `sm+`.
- No page-level horizontal scrolling is accepted at 320 CSS pixels or wider.
  Breadcrumbs and other inherently wide, non-tabular content may own a bounded
  local scroll region when reflow is not practical.

## 5. Component Primitives

### Application Shell

The desktop sidebar remains 16rem wide at `lg+`. Below `lg`, the same navigation
becomes an off-canvas drawer controlled by one Alpine state in the application
shell. The main content always keeps `min-w-0` and prevents accidental horizontal
overflow.

### Header And Navigation Drawer

The mobile header provides a clearly named menu button with `aria-controls` and
`aria-expanded`. The drawer has a stable ID, closes from its backdrop and Escape,
and returns focus to the trigger. Search and profile access remain available;
secondary identity detail may hide where space is constrained.

### Tables

Drive files, Dashboard recent documents, Shared items, Trash documents, Activity
log, Admin users, Admin unit kerja, and Admin categories use one responsive data
primitive. Below `lg`, each collection renders as a compact labelled card/list
that reflows within its container without two-dimensional scrolling. At `lg+`,
the complete semantic table remains available and must fit the sidebar-reduced
content area at 1024px without a nested horizontal scroller. Table columns use
compact spacing and wrap complete primary data and actions instead of truncating
them off-screen.

Both presentations preserve every applicable field, action, form, confirmation,
permission condition, pagination behavior, route, and Alpine interaction. Data
and controls may be regrouped for narrow screens, but cannot be hidden or made
unreachable. This rule applies only to the eight collections above; unrelated
wide content may retain a bounded local scroll region where necessary.

### Breadcrumbs, Overlays, And Upload Status

Breadcrumbs use a local horizontal scroll or bounded truncation. Modal panels are
viewport-height constrained and vertically scrollable, with one-column forms on
mobile and two columns from `sm`. Fixed upload status panels use viewport-safe
insets and widths on mobile, returning to their current desktop size at `sm+`.

## 6. Responsive Behavior

- Mobile baseline: 320-639px; single-column content and stacked actions.
- Small/tablet: 640-767px; horizontal actions where they fit.
- Tablet: 768-1023px; compact header and off-canvas navigation remain active.
- Desktop: 1024px and wider (`lg`); existing sidebar and desktop composition are
  preserved.
- Required QA widths: 375px, 768px, 1023px, 1024px, and 1280px.
- The 1023/1024 boundary must switch cleanly between drawer and static sidebar
  without duplicate visible navigation or a backdrop on desktop.
- Drive folder cards remain two columns at the 1024px boundary and expand to four
  columns only when the content area is wide enough at `xl`.

## 7. Motion And Interaction

- Drawer and backdrop transitions animate only `transform` and `opacity`.
- Motion communicates opening, closing, or state changes; decorative animation is
  not introduced.
- `prefers-reduced-motion` disables nonessential transition duration.
- Menus, dialogs, and drawers must support keyboard operation, Escape dismissal,
  visible focus, and focus restoration where an overlay temporarily takes focus.
- Touch users retain the same document and folder actions exposed to pointer users.

## 8. Accessibility, Debt, And Handoff

- Interactive icon-only controls require accessible names; `title` alone is not
  sufficient.
- Dialogs require `role="dialog"`, `aria-modal="true"`, a labelled heading, a
  reachable close path, and viewport-safe scrolling.
- The drawer backdrop cannot trap focus behind the drawer or leave page scrolling
  active while navigation is open.
- Existing custom Blade/Alpine modals are accepted design debt for this responsive
  pass. They should receive consistent sizing and semantics without an unrelated
  component-system rewrite.
- Desktop semantic tables remain intentionally dense at `lg+`, but the eight
  named collections must fit their content area without nested horizontal
  scrolling at the 1024px boundary.
- Final handoff requires browser evidence at the required widths, keyboard drawer
  checks, modal checks, and confirmation that the 1280px desktop layout is stable.
