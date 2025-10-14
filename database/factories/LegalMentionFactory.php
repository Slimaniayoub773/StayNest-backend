<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegalMention>
 */
class LegalMentionFactory extends Factory
{
    protected $model = \App\Models\LegalMention::class;

    public function definition(): array
    {
        $titles = [
            'Privacy Policy',
            'Terms and Conditions',
            'Cancellation Policy',
            'Refund Policy',
            'Data Protection',
            'Guest Responsibilities',
            'Liability Disclaimer',
            'Cookies Policy',
            'Health & Safety Regulations',
            'Booking Agreement',
        ];

        $title = $this->faker->randomElement($titles);

        $content = match($title) {
            'Privacy Policy' => 'We value your privacy. Personal data collected during booking and stay will only be used to provide services and will not be shared with third parties without consent.',
            'Terms and Conditions' => 'By using our services, you agree to abide by all hotel rules and regulations, including payment policies and conduct requirements.',
            'Cancellation Policy' => 'Guests may cancel reservations up to 48 hours before check-in without penalty. Late cancellations may incur charges according to the booking terms.',
            'Refund Policy' => 'Refunds will be processed within 14 business days. Certain fees may be non-refundable depending on the booking type.',
            'Data Protection' => 'All personal and payment information is stored securely and processed in compliance with applicable data protection laws.',
            'Guest Responsibilities' => 'Guests are responsible for any damage to hotel property and must follow all safety guidelines during their stay.',
            'Liability Disclaimer' => 'The hotel is not liable for personal belongings lost or damaged during the stay unless negligence can be proven.',
            'Cookies Policy' => 'Our website uses cookies to enhance user experience, analyze traffic, and personalize content. Users may opt out at any time.',
            'Health & Safety Regulations' => 'All guests must follow safety protocols and local health regulations. The hotel reserves the right to refuse service for safety violations.',
            'Booking Agreement' => 'Booking a room constitutes acceptance of all hotel policies, including payment, cancellation, and conduct rules.',
            default => $this->faker->paragraphs(3, true),
        };

        return [
            'title' => $title,
            'content' => $content,
        ];
    }
}
