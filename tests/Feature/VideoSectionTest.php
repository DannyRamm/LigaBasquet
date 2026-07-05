<?php

namespace Tests\Feature;

use Tests\TestCase;

class VideoSectionTest extends TestCase
{
    public function test_el_archivo_de_videos_en_public_tiene_la_estructura_del_modal_y_su_script_externo()
    {
        // 1. Apuntamos al archivo PHP
        $filePath = public_path('pages/videos.php');
        $this->assertFileExists($filePath);
        $fileContent = file_get_contents($filePath);

        // 2. Verificamos los componentes HTML del modal
        $this->assertStringContainsString('id="videoModal"', $fileContent);
        $this->assertStringContainsString('id="youtubeFrame"', $fileContent);
        $this->assertStringContainsString('data-video=', $fileContent);

        // 3. Verificamos que esté llamando correctamente a tu nuevo archivo JS externo
        $this->assertStringContainsString('<script src="assets/js/videos.js"></script>', $fileContent);
    }
}