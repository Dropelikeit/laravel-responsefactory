<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Services;

use Dropelikeit\ResponseFactory\Contracts\Decorators\MimetypeFromFileInformationDetector;
use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Factories\Transformers\InputToStringTransformerFactory;
use Dropelikeit\ResponseFactory\Contracts\Services\MimeTypeDetector as MimeTypeDetectorContract;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final readonly class MimeTypeDetector implements MimeTypeDetectorContract
{
    public function __construct(
        private InputToStringTransformerFactory $inputToStringTransformerFactory,
        private MimetypeFromFileInformationDetector $mimetypeFromFileInformationDetector
    ) {

    }

    /**
     * @psalm-return non-empty-string
     */
    public function detect(Input $toDetect): string
    {
        $transformer = $this->inputToStringTransformerFactory->factorize($toDetect);

        $content = $transformer->transform($toDetect);

        $mimetype = $this->mimetypeFromFileInformationDetector->fetchMimetypeByGivenString($content);

        return $mimetype->getMimetype();
    }
}
