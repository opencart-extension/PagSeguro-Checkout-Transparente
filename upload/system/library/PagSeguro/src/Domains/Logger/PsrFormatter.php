<?php

namespace ValdeirPsr\PagSeguro\Domains\Logger;

use Monolog\Formatter\HtmlFormatter;

class PsrFormatter extends HtmlFormatter
{
    /**
     * {@inheritDoc}
     */
    protected function addRow(string $th, string $td = ' ', bool $escapeTd = true): string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre style="white-space: pre-wrap;">' . htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8') . '</pre>';
        }

        return "<tr style=\"padding: 4px;text-align: left;\">\n" .
        "<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">$th:</th>\n" .
        "<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">" .
        $td . "</td>\n</tr>";
    }

    /**
     * {@inheritDoc}
     */
    public function format(array $record): string
    {
        $output = '<div data-level="' . $record['level'] . '">';
        $output .= $this->addTitle($record['level_name'], $record['level']);
        $output .= '<table cellspacing="1" width="100%" class="monolog-output table">';

        $output .= $this->addRow('Message', (string) $record['message']);
        $output .= $this->addRow('Time', $this->formatDate($record['datetime']));
        $output .= $this->addRow('Channel', $record['channel']);
        if ($record['context']) {
            $embeddedTable = '<table cellspacing="1" width="100%" class="table">';
            foreach ($record['context'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if ($record['extra']) {
            $embeddedTable = '<table cellspacing="1" width="100%" class="table">';
            foreach ($record['extra'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }

        return $output . '</table></div>';
    }
}
