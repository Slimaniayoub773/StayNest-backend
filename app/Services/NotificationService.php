<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Review;
use App\Models\CleaningSchedule;
use App\Models\User;
use App\Models\Role;
use App\Notifications\BookingConfirmationNotification;
use App\Notifications\BookingCancellationNotification;
use App\Notifications\GuestCheckinNotification;
use App\Notifications\GuestCheckoutNotification;
use App\Notifications\GuestRegisteredNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\ReviewNotification;
use App\Notifications\CleaningRequestNotification;
use App\Notifications\FeedbackNotification;
use App\Notifications\RoomAvailableNotification;
use App\Notifications\SystemIssueNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification to a specific user
     */
    public function send($user, $message)
    {
        try {
            // Basic notification implementation
            Log::info('Notification sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'message' => $message
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify about new booking with details
     */
    public function notifyNewBookingWithDetails(Booking $booking)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new BookingConfirmationNotification($booking));
            }
            
            Log::info('New booking notification sent', [
                'booking_id' => $booking->id,
                'room_number' => $booking->room->room_number,
                'guest_name' => $booking->guest->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send new booking notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about booking cancellation
     */
    public function notifyBookingCancelled(Booking $booking, string $reason = null)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new BookingCancellationNotification($booking, $reason));
            }
            
            Log::info('Booking cancellation notification sent', [
                'booking_id' => $booking->id,
                'reason' => $reason
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about guest check-in
     */
    public function notifyGuestCheckIn(Booking $booking)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new GuestCheckinNotification($booking));
            }
            
            Log::info('Guest check-in notification sent', [
                'booking_id' => $booking->id,
                'room_number' => $booking->room->room_number
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send guest check-in notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about guest check-out
     */
    public function notifyGuestCheckOut(Booking $booking)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new GuestCheckoutNotification($booking));
            }
            
            Log::info('Guest check-out notification sent', [
                'booking_id' => $booking->id,
                'room_number' => $booking->room->room_number
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send guest check-out notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about new guest registration
     */
    public function notifyNewGuestRegistration(Guest $guest)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new GuestRegisteredNotification($guest));
            }
            
            Log::info('New guest registration notification sent', [
                'guest_id' => $guest->id,
                'guest_email' => $guest->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send guest registration notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about payment received
     */
    public function notifyPaymentReceived(Payment $payment)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new PaymentReceivedNotification($payment));
            }
            
            Log::info('Payment received notification sent', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'booking_id' => $payment->booking_id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment received notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about payment failure with details
     */
    public function notifyPaymentFailedWithDetails(Payment $payment, string $errorMessage, int $retryCount = 0)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new PaymentFailedNotification($payment, $errorMessage, $retryCount));
            }
            
            Log::warning('Payment failed notification sent', [
                'payment_id' => $payment->id,
                'error_message' => $errorMessage,
                'retry_count' => $retryCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about new review
     */
    public function notifyNewReview(Review $review)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new FeedbackNotification($review));
            }
            
            Log::info('New review notification sent', [
                'review_id' => $review->id,
                'rating' => $review->rating,
                'room_id' => $review->room_id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send new review notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about urgent complaint (low rating review)
     */
    public function notifyUrgentComplaint(Review $review)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new FeedbackNotification(
                    $review,
                    'Urgent Complaint - Low Rating',
                    'Guest left a low rating review that requires immediate attention',
                    'urgent'
                ));
            }
            
            Log::warning('Urgent complaint notification sent', [
                'review_id' => $review->id,
                'rating' => $review->rating,
                'room_id' => $review->room_id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send urgent complaint notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about cleaning request
     */
    public function notifyCleaningRequired(Room $room, string $reason = null)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new CleaningRequestNotification(
                    $room,
                    'standard',
                    'high',
                    $reason ?? 'Cleaning required'
                ));
            }
            
            Log::info('Cleaning required notification sent', [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'reason' => $reason
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send cleaning required notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about room availability
     */
    public function notifyRoomAvailable(Room $room)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new RoomAvailableNotification($room));
            }
            
            Log::info('Room available notification sent', [
                'room_id' => $room->id,
                'room_number' => $room->room_number
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send room available notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about system issues
     */
    public function notifySystemIssue(string $message, string $severity = 'error')
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new SystemIssueNotification(
                    'System Issue - ' . ucfirst($severity),
                    $message,
                    'system',
                    $severity === 'error' ? 'high' : 'normal'
                ));
            }
            
            Log::error('System issue notification sent', [
                'message' => $message,
                'severity' => $severity
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send system issue notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about maintenance required
     */
    public function notifyMaintenanceRequired(Room $room, string $issueDescription)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new CleaningRequestNotification(
                    $room,
                    'maintenance',
                    'high',
                    $issueDescription
                ));
            }
            
            Log::warning('Maintenance required notification sent', [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'issue' => $issueDescription
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send maintenance required notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify about cleaning schedule
     */
    public function notifyCleaningScheduled(CleaningSchedule $cleaningSchedule)
    {
        try {
            $adminUsers = $this->getAdminUsers();
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new CleaningRequestNotification(
                    $cleaningSchedule->room,
                    'scheduled_cleaning',
                    'normal',
                    'Cleaner: ' . ($cleaningSchedule->cleaner->name ?? 'Not assigned')
                ));
            }
            
            Log::info('Cleaning scheduled notification sent', [
                'cleaning_id' => $cleaningSchedule->id,
                'room_number' => $cleaningSchedule->room->room_number,
                'scheduled_date' => $cleaningSchedule->cleaning_date
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send cleaning scheduled notification: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk notifications to multiple users
     */
    public function sendBulkNotification(array $users, string $title, string $message, string $category = 'general')
    {
        try {
            $notification = new SystemIssueNotification($title, $message, $category, 'normal');
            
            foreach ($users as $user) {
                $user->notify($notification);
            }
            
            Log::info('Bulk notification sent', [
                'user_count' => count($users),
                'title' => $title,
                'category' => $category
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send bulk notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get admin users for notifications
     */
    private function getAdminUsers()
    {
        try {
            $adminRole = Role::where('role_name', 'admin')->first();
            
            if ($adminRole) {
                return User::where('role_id', $adminRole->id)
                    ->where('is_active', true)
                    ->get();
            }
            
            // Fallback: get users with admin privileges
            return User::where('is_admin', true)
                ->orWhere('email', 'like', '%@admin.%')
                ->where('is_active', true)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Failed to get admin users: ' . $e->getMessage());
            return collect(); // Return empty collection
        }
    }

    /**
     * Get users by role for targeted notifications
     */
    public function getUsersByRole(string $roleName)
    {
        try {
            $role = Role::where('role_name', $roleName)->first();
            
            if ($role) {
                return User::where('role_id', $role->id)
                    ->where('is_active', true)
                    ->get();
            }
            
            return collect();
            
        } catch (\Exception $e) {
            Log::error('Failed to get users by role: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Check if notifications are enabled for a user
     */
    public function areNotificationsEnabled($user): bool
    {
        try {
            // You can implement user preference checks here
            return $user->notification_preferences ?? true;
        } catch (\Exception $e) {
            Log::error('Failed to check notification preferences: ' . $e->getMessage());
            return true; // Default to enabled
        }
    }
}