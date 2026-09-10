<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ResponsiveLayoutTest extends TestCase
{
    public function test_application_shell_defines_mobile_navigation_state(): void
    {
        // Given
        $view = $this->readView('layouts/app.blade.php');

        // When
        $hasResponsiveShell = str_contains($view, 'sidebarOpen: false')
            && str_contains($view, 'overflow-x-hidden')
            && str_contains($view, 'p-4 sm:p-6 md:p-8');

        // Then
        $this->assertTrue($hasResponsiveShell);
    }

    public function test_sidebar_switches_from_drawer_to_static_desktop_navigation(): void
    {
        // Given
        $view = $this->readView('layouts/sidebar.blade.php');

        // When
        $hasResponsiveSidebar = str_contains($view, 'id="app-sidebar"')
            && str_contains($view, '-translate-x-full')
            && str_contains($view, 'lg:translate-x-0')
            && str_contains($view, 'lg:static');

        // Then
        $this->assertTrue($hasResponsiveSidebar);
    }

    public function test_closed_mobile_sidebar_is_hidden_from_keyboard_navigation(): void
    {
        $appView = $this->readView('layouts/app.blade.php');
        $sidebarView = $this->readView('layouts/sidebar.blade.php');

        $this->assertStringContainsString('isDesktop:', $appView);
        $this->assertStringContainsString(':inert="!isDesktop && !sidebarOpen"', $sidebarView);
        $this->assertStringContainsString(':aria-hidden="(!isDesktop && !sidebarOpen).toString()"', $sidebarView);
    }

    public function test_header_exposes_accessible_mobile_menu_trigger(): void
    {
        // Given
        $view = $this->readView('layouts/header.blade.php');

        // When
        $hasMenuTrigger = str_contains($view, 'aria-controls="app-sidebar"')
            && str_contains($view, ':aria-expanded="sidebarOpen"')
            && str_contains($view, 'lg:hidden')
            && str_contains($view, 'px-4 sm:px-6 lg:px-8');

        // Then
        $this->assertTrue($hasMenuTrigger);
    }

    public function test_drive_upload_panel_stays_inside_the_viewport(): void
    {
        // Given
        $view = $this->readView('drive/index.blade.php');

        // When
        $hasSafeUploadPanel = str_contains($view, 'max-w-[calc(100vw-2rem)]');

        // Then
        $this->assertTrue($hasSafeUploadPanel);
    }

    public function test_data_tables_use_compact_lists_below_desktop(): void
    {
        // Given
        $responsiveDataViews = [
            'drive/index.blade.php',
            'dashboard.blade.php',
            'drive/shared.blade.php',
            'drive/trash.blade.php',
            'activity/index.blade.php',
            'admin/users/index.blade.php',
            'admin/unit-kerja/index.blade.php',
            'admin/categories/index.blade.php',
        ];

        // When / Then
        foreach ($responsiveDataViews as $relativePath) {
            $view = $this->readView($relativePath);
            $compactTag = $this->readOpeningTag($view, 'data-responsive-card-list', $relativePath);
            $desktopTag = $this->readOpeningTag($view, 'data-responsive-desktop-table', $relativePath);
            $compactMarkup = $this->readMarkupBetweenMarkers(
                $view,
                'data-responsive-card-list',
                'data-responsive-desktop-table',
                $relativePath
            );
            $desktopMarkup = $this->readDesktopTableMarkup($view, $relativePath);

            $this->assertStringContainsString('lg:hidden', $compactTag, $relativePath);
            $this->assertStringNotContainsString('<table', strtolower($compactMarkup), $relativePath);
            $this->assertStringContainsString('hidden', $desktopTag, $relativePath);
            $this->assertStringContainsString('lg:block', $desktopTag, $relativePath);
            $this->assertStringNotContainsString('overflow-x-auto', $desktopTag, $relativePath);
            $this->assertStringContainsString('<table', $desktopMarkup, $relativePath);
            $this->assertStringContainsString('table-fixed', $desktopMarkup, $relativePath);
            $this->assertStringNotContainsString('min-w-[', $desktopMarkup, $relativePath);
            $this->assertStringNotContainsString('truncate', $desktopMarkup, $relativePath);
        }
    }

    public function test_drive_actions_serialize_names_and_name_the_share_close_button(): void
    {
        $view = $this->readView('drive/index.blade.php');
        $shareCloseTag = $this->readOpeningTag($view, 'aria-label="Close share dialog"', 'drive/index.blade.php');

        $this->assertStringNotContainsString("activeFolderName = '{{ \$f->nama }}'", $view);
        $this->assertStringNotContainsString("'{{ addslashes(\$f->nama) }}'", $view);
        $this->assertStringNotContainsString("'{{ addslashes(\$doc->nama) }}'", $view);
        $this->assertGreaterThanOrEqual(4, substr_count($view, 'Js::from($f->nama)'));
        $this->assertStringNotContainsString("activeFileName = '{{ \$doc->nama }}'", $view);
        $this->assertGreaterThanOrEqual(6, substr_count($view, 'Js::from($doc->nama)'));
        $this->assertStringContainsString('type="button"', $shareCloseTag);
    }

    public function test_drive_action_menus_dismiss_with_escape_and_restore_focus(): void
    {
        $view = $this->readView('drive/index.blade.php');

        $this->assertGreaterThanOrEqual(3, substr_count($view, 'x-ref="actionsTrigger"'));
        $this->assertGreaterThanOrEqual(3, substr_count($view, 'focus-visible:ring-2 focus-visible:ring-indigo-500'));
        $this->assertGreaterThanOrEqual(3, substr_count($view, '@keydown.escape.stop.prevent="open = false; $refs.actionsTrigger.focus()"'));
        $this->assertGreaterThanOrEqual(6, substr_count($view, 'dialogReturnFocus = $refs.actionsTrigger'));
        $this->assertStringContainsString('returnFocus: null', $view);
        $this->assertStringContainsString('this.contextMenu.returnFocus = e.currentTarget.querySelector', $view);
        $this->assertStringContainsString('closeContextMenu(nextTick)', $view);
        $this->assertGreaterThanOrEqual(6, substr_count($view, 'dialogReturnFocus = contextMenu.returnFocus'));
        $this->assertStringContainsString('if (!this.dialogReturnFocus) this.dialogReturnFocus = document.activeElement;', $view);
        $this->assertStringContainsString('restoreDialogFocus(nextTick)', $view);
        $this->assertSame(6, substr_count($view, 'restoreDialogFocus($nextTick)'));
        $this->assertStringNotContainsString('setTimeout(() => returnFocus.focus()', $view);
        $this->assertStringContainsString('const dialogIsOpen = this.showCreateFolder || this.showUpload', $view);
        $this->assertStringContainsString('if (!dialogIsOpen) return;', $view);
    }

    public function test_trash_folder_grid_stays_readable_at_desktop_boundary(): void
    {
        $view = $this->readView('drive/trash.blade.php');

        $this->assertStringContainsString('grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4', $view);
        $this->assertStringContainsString('title="{{ $f->nama }}"', $view);
        $this->assertStringContainsString('xl:flex-col xl:items-stretch xl:gap-3 2xl:flex-row 2xl:items-center', $view);
        $this->assertStringContainsString('xl:justify-end 2xl:justify-start', $view);
        $this->assertStringContainsString('min-w-0 flex-1', $view);
        $this->assertStringContainsString('break-words text-sm font-semibold text-slate-600', $view);
        $this->assertStringNotContainsString('space-x-3 truncate flex-1', $view);
        $this->assertStringNotContainsString('class="truncate text-sm font-semibold text-slate-600"', $view);
    }

    public function test_activity_action_badges_wrap_inside_their_table_cell(): void
    {
        $view = $this->readView('activity/index.blade.php');

        $this->assertStringContainsString(
            'inline-block max-w-full break-all px-2.5 py-0.5 rounded-full',
            $view
        );
    }

    public function test_drive_modals_reflow_and_scroll_inside_short_viewports(): void
    {
        // Given
        $view = $this->readView('drive/index.blade.php');

        // When
        $hasResponsiveFormGrid = str_contains($view, 'grid-cols-1 sm:grid-cols-2');
        $hasViewportSafePanel = str_contains($view, 'max-h-[calc(100vh-2rem)]');

        // Then
        $this->assertTrue($hasResponsiveFormGrid);
        $this->assertTrue($hasViewportSafePanel);
    }

    public function test_dashboard_header_stacks_before_small_breakpoint(): void
    {
        // Given
        $view = $this->readView('dashboard.blade.php');

        // When
        $hasResponsiveHeader = str_contains(
            $view,
            'flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between'
        );

        // Then
        $this->assertTrue($hasResponsiveHeader);
    }

    public function test_application_shell_releases_scroll_lock_at_desktop_breakpoint(): void
    {
        // Given
        $view = $this->readView('layouts/app.blade.php');

        // When
        $handlesDesktopResize = str_contains($view, '@resize.window="syncSidebarViewport()"')
            && str_contains($view, ':inert="sidebarOpen"');

        // Then
        $this->assertTrue($handlesDesktopResize);
    }

    public function test_closed_mobile_sidebar_does_not_cast_a_shadow(): void
    {
        // Given
        $view = $this->readView('layouts/sidebar.blade.php');

        // When
        $hasStatefulShadow = str_contains($view, "'translate-x-0 shadow-2xl'")
            && str_contains($view, "'-translate-x-full shadow-none'");

        // Then
        $this->assertTrue($hasStatefulShadow);
    }

    public function test_font_token_and_public_pages_use_instrument_sans(): void
    {
        // Given
        $tailwindConfig = $this->readProjectFile('tailwind.config.js');
        $accessView = $this->readView('public-link/access.blade.php');
        $passwordView = $this->readView('public-link/password.blade.php');

        // When
        $usesInstrumentSans = str_contains($tailwindConfig, "sans: ['Instrument Sans'")
            && str_contains($accessView, 'instrument-sans:400,500,600,700')
            && str_contains($passwordView, 'instrument-sans:400,500,600,700');

        // Then
        $this->assertTrue($usesInstrumentSans);
    }

    public function test_table_metadata_and_icon_actions_have_responsive_accessibility_guards(): void
    {
        // Given
        $dashboardView = $this->readView('dashboard.blade.php');
        $driveView = $this->readView('drive/index.blade.php');
        $activityView = $this->readView('activity/index.blade.php');

        // When
        $hasStableMetadata = str_contains($dashboardView, 'class="px-2 py-3 xl:px-6">Uploaded At')
            && str_contains($driveView, 'class="px-2 py-3 xl:px-6">Last Modified')
            && str_contains($activityView, 'class="px-2 py-3 xl:px-6">Timestamp');
        $hasNamedActions = str_contains($driveView, 'aria-label="Open folder actions')
            && str_contains($driveView, 'aria-label="Open file actions');

        // Then
        $this->assertTrue($hasStableMetadata);
        $this->assertTrue($hasNamedActions);
    }

    private function readView(string $relativePath): string
    {
        return $this->readProjectFile('resources/views/'.$relativePath);
    }

    private function readOpeningTag(string $view, string $marker, string $relativePath): string
    {
        $markerPosition = strpos($view, $marker);

        $this->assertNotFalse($markerPosition, $relativePath.' is missing '.$marker);

        $tagStart = strrpos(substr($view, 0, $markerPosition), '<');
        $tagEnd = strpos($view, '>', $markerPosition);

        $this->assertNotFalse($tagStart, $relativePath.' has an invalid '.$marker.' marker');
        $this->assertNotFalse($tagEnd, $relativePath.' has an invalid '.$marker.' marker');

        return substr($view, $tagStart, $tagEnd - $tagStart + 1);
    }

    private function readMarkupBetweenMarkers(
        string $view,
        string $startMarker,
        string $endMarker,
        string $relativePath
    ): string {
        $start = strpos($view, $startMarker);
        $end = strpos($view, $endMarker);

        $this->assertNotFalse($start, $relativePath.' is missing '.$startMarker);
        $this->assertNotFalse($end, $relativePath.' is missing '.$endMarker);
        $this->assertGreaterThan($start, $end, $relativePath.' has markers in the wrong order');

        return substr($view, $start, $end - $start);
    }

    private function readDesktopTableMarkup(string $view, string $relativePath): string
    {
        $start = strpos($view, 'data-responsive-desktop-table');
        $tableEnd = strpos($view, '</table>', $start === false ? 0 : $start);

        $this->assertNotFalse($start, $relativePath.' is missing the desktop marker');
        $this->assertNotFalse($tableEnd, $relativePath.' is missing the desktop table closing tag');

        return substr($view, $start, $tableEnd - $start + strlen('</table>'));
    }

    private function readProjectFile(string $relativePath): string
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/'.$relativePath);

        $this->assertIsString($view);

        return $view;
    }
}
