<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;

/**
 * Stamps a diagonal "ORA" watermark across every page of a PDF,
 * on the fly, without touching the original stored file.
 *
 * Requires (run once): composer require setasign/fpdi tecnickcom/tcpdf
 */
class PdfWatermarkService
{
    public function __construct(
        protected string $text = 'ORA REPOSITORY',
    ) {
    }

    /**
     * Produce a watermarked copy of the given PDF and return the
     * absolute path to the temporary file. Caller is responsible for
     * deleting it after use (e.g. response()->download(..., deleteFileAfterSend: true)).
     */
    public function stamp(string $sourceAbsolutePath): string
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pageCount = $pdf->setSourceFile($sourceAbsolutePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $this->drawWatermark($pdf, $size['width'], $size['height']);
        }

        $tmpDir = storage_path('app/private/tmp');
        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $outputPath = $tmpDir.'/'.uniqid('ora-watermarked-', true).'.pdf';
        $pdf->Output($outputPath, 'F');

        return $outputPath;
    }

    protected function drawWatermark(Fpdi $pdf, float $width, float $height): void
    {
        $pdf->SetFont('helvetica', 'B', 60);
        $pdf->SetTextColor(160, 160, 160);

        // 25% opacity so the underlying content stays fully readable.
        $pdf->setAlpha(0.25);

        $pdf->StartTransform();
        $pdf->Rotate(45, $width / 2, $height / 2);
        $pdf->Text(
            ($width / 2) - (strlen($this->text) * 12),
            $height / 2,
            $this->text
        );
        $pdf->StopTransform();

        $pdf->setAlpha(1);
    }
}
