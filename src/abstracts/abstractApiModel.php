<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;

abstract class abstractApiModel extends abstractModel {
    /**
     * @return array
     */
    public function DELETE(): array {
        errorReporter::returnHttpCode(405, 'DELETE method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function GET(): array {
        errorReporter::returnHttpCode(405, 'GET method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function POST(): array {
        errorReporter::returnHttpCode(405, 'POST method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function PUT(): array {
        errorReporter::returnHttpCode(405, 'PUT method not allowed');
        exit;
    }
}