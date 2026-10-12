<?php

namespace App\Forms\Exporters;

use League\Csv\Bom;
use League\Csv\EscapeFormula;
use League\Csv\Writer;
use SplTempFileObject;
use Statamic\Contracts\Forms\Submission;
use Statamic\Forms\Exporters\Exporter;

class ScalbyWalkCsvExporter extends Exporter
{
    protected static string $title = 'Walk registrations (Excel-ready CSV)';

    public function export(): string
    {
        $writer = Writer::createFromFileObject(new SplTempFileObject);
        $writer->setOutputBOM(Bom::Utf8);
        $writer->addFormatter(new EscapeFormula("'"));
        $writer->insertOne($this->headers());

        $this->submissions()->each(function (Submission $submission) use ($writer): void {
            $participants = $this->participants($submission);

            if ($participants === []) {
                $participants[] = $this->participant();
            }

            foreach ($participants as $participant) {
                $writer->insertOne($this->row($submission, $participant));
            }
        });

        return (string) $writer;
    }

    public function extension(): string
    {
        return 'csv';
    }

    public function contentType(): string
    {
        return 'text/csv; charset=UTF-8';
    }

    /** @return array<int, string> */
    private function headers(): array
    {
        return [
            'Booking reference',
            'Submitted at',
            'Payment status',
            'Paid at',
            'Payment reference',
            'Registrant first name',
            'Registrant last name',
            'Email',
            'Phone',
            'Address line 1',
            'Address line 2',
            'Town or city',
            'County',
            'Postcode',
            'Country',
            'Participant type',
            'Participant first name',
            'Participant last name',
            'Age',
            'Gender',
            'Postcode area',
            'Adult walkers',
            'Under-18 walkers',
            'Dogs',
            'Donation (£)',
            'Total paid (£)',
            'Walker details confirmed',
            'Rules confirmed',
        ];
    }

    /** @param array{type: string, first_name: string, last_name: string, age: int|string, gender: string, postcode: string} $participant */
    private function row(Submission $submission, array $participant): array
    {
        return [
            $submission->id(),
            $submission->date()->format('Y-m-d H:i:s'),
            $submission->get('payment_status'),
            $submission->get('paid_at'),
            $submission->get('stripe_payment_intent_id'),
            $submission->get('first_name'),
            $submission->get('last_name'),
            $submission->get('email'),
            $submission->get('phone'),
            $submission->get('address_line_1'),
            $submission->get('address_line_2'),
            $submission->get('town'),
            $submission->get('county'),
            $submission->get('postcode'),
            $submission->get('country'),
            $participant['type'],
            $participant['first_name'],
            $participant['last_name'],
            $participant['age'],
            $participant['gender'],
            $participant['postcode'],
            (int) $submission->get('adult_count'),
            (int) $submission->get('junior_count'),
            (int) $submission->get('dog_count'),
            ((int) $submission->get('donation_pence')) / 100,
            ((int) $submission->get('total_pence')) / 100,
            $submission->get('walker_details_confirmation') ? 'Yes' : 'No',
            $submission->get('rules_confirmation') ? 'Yes' : 'No',
        ];
    }

    /** @return array<int, array{type: string, first_name: string, last_name: string, age: int|string, gender: string, postcode: string}> */
    private function participants(Submission $submission): array
    {
        $participants = [];

        foreach ((array) $submission->get('adult_walkers') as $walker) {
            $participants[] = $this->participant('Adult', (array) $walker);
        }

        foreach ((array) $submission->get('junior_walkers') as $walker) {
            $participants[] = $this->participant('Under-18', (array) $walker);
        }

        foreach ((array) $submission->get('dogs') as $dog) {
            $participants[] = $this->participant('Dog', [
                'first_name' => $dog['name'] ?? '',
                'age' => $dog['age'] ?? '',
            ]);
        }

        return $participants;
    }

    /** @return array{type: string, first_name: string, last_name: string, age: int|string, gender: string, postcode: string} */
    private function participant(string $type = '', array $data = []): array
    {
        return [
            'type' => $type,
            'first_name' => (string) ($data['first_name'] ?? ''),
            'last_name' => (string) ($data['last_name'] ?? ''),
            'age' => $data['age'] ?? '',
            'gender' => (string) ($data['gender'] ?? ''),
            'postcode' => (string) ($data['postcode'] ?? ''),
        ];
    }
}
