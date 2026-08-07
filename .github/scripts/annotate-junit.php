<?php

declare(strict_types=1);

/**
 * This script takes the JUnit-formatted output from PHPUnit tests,
 * and converts it to the GitHub annotations format, so that test
 * results show up nicely in GH Actions' output for PRs.
 */
$file = $argv[1] ?? 'build/logs/junit.xml';
if (!file_exists($file)) {
    exit(0);
}

$xml = simplexml_load_file($file);
if ($xml === false) {
    exit(0);
}

// Collected failures for the step summary
/** @var list<array{name: string, file: string, line: int, message: string }> $failures */
$failures = [];

foreach ($xml->xpath('//testcase') as $case) {
    foreach ($case->xpath('failure|error') as $problem) {
        $src_file = str_replace(getcwd() . '/', '', (string) $case['file']);
        $line = (int) ($case['line'] ?: 1);
        $name = (string) $case['name'];
        $raw = trim((string) $problem);

        // PHPUnit appends the failure location as a trailing line using the
        // absolute path. We already know the relative path and line number,
        // so we can strip it to avoid a confusing absolute path in the summary.
        $abs = (string) $case['file'];
        if ($abs !== '') {
            $raw = preg_replace(
                '/\R+' . preg_quote($abs, '/') . ':\d+\s*$/',
                '',
                $raw
            );
            $raw = rtrim($raw);
        }

        // GH annotation output
        $msg = str_replace(
            ['%', "\r", "\n"],
            ['%25', '%0D', '%0A'],
            $raw
        );
        printf(
            "::error file=%s,line=%s,title=%s::%s\n",
            $src_file,
            $line,
            $case['name'],
            $msg
        );

        // keep for Markdown summary
        $failures[] = [
            'name'    => $name,
            'file'    => $src_file,
            'line'    => $line,
            'message' => $raw,
        ];
    }
}

// Write a Markdown summary to the step summary page, if available and
// there were any failures.
$summary_file = getenv('GITHUB_STEP_SUMMARY');
if ($summary_file !== false && $failures !== []) {
    $out = "### ❌ Test Failures\n\n"
        . "The following PHPUnit tests failed and need to be addressed:\n\n";

    foreach ($failures as $failure) {
        $out .= sprintf(
            "#### %s\n%s:%d\n```%s```\n\n",
            $failure['name'],
            $failure['file'],
            $failure['line'],
            $failure['message']
        );
    }

    file_put_contents($summary_file, $out, FILE_APPEND);
}
