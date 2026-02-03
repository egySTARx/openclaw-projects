#!/usr/bin/env node
/**
 * Meeting Manager for Memo
 * - Check Google Calendar for upcoming meetings
 * - Send reminders to Telegram
 * - Show meeting details
 */

// Configuration
const config = {
  telegramChatId: '5039963815', // TechnoMeeM Tech Support
  checkInterval: parseInt(process.env.CHECK_INTERVAL || '3600000'), // 1 hour default
  reminderHours: parseInt(process.env.REMINDER_HOURS || '1'), // Reminder 1 hour before
  meetingsToCheck: process.env.MEETINGS_TO_CHECK || 'all', // 'all', 'today', 'tomorrow'
  timezones: process.env.TIMEZONES || 'UTC',
  language: process.env.LANGUAGE || 'en',
  googleCalendarApiKey: process.env.GOOGLE_CALENDAR_API_KEY || '',
  calendarId: process.env.CALENDAR_ID || 'primary'
};

let lastChecked = Date.now();
let checkedMeetings = new Set();

/**
 * Check Google Calendar for upcoming meetings
 */
async function checkMeetings() {
  console.log(`Checking meetings at ${new Date().toISOString()}...`);

  if (!config.googleCalendarApiKey) {
    console.log('WARNING: No Google Calendar API key configured');
    console.log('Please set GOOGLE_CALENDAR_API_KEY in scripts/.env');
    return 0;
  }

  try {
    // Fetch events from Google Calendar API
    const url = `https://www.googleapis.com/calendar/v3/calendars/${config.calendarId}/events?timeMin=${new Date().toISOString()}&timeMax=${new Date(Date.now() + 7*24*60*60*1000).toISOString()}&orderBy=startTime&singleEvents=true&maxResults=50`;

    const response = await fetch(url, {
      headers: {
        'Authorization': `Bearer ${config.googleCalendarApiKey}`
      }
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error?.message || 'Failed to fetch events');
    }

    const data = await response.json();
    const meetings = data.items || [];

    if (meetings.length > 0) {
      await sendMeetingReminders(meetings);
    }

    lastChecked = Date.now();
    checkedMeetings.clear();
    console.log(`Found ${meetings.length} meetings`);
    return meetings.length;

  } catch (error) {
    console.error('Error checking meetings:', error.message);
    return 0;
  }
}

/**
 * Send meeting reminders to Telegram
 */
async function sendMeetingReminders(meetings) {
  for (const meeting of meetings) {
    if (checkedMeetings.has(meeting.id)) continue;

    const now = Date.now();
    const meetingTime = new Date(meeting.start?.dateTime || meeting.start?.date).getTime();
    const timeDiff = meetingTime - now;

    // Send reminder X hours before
    const reminderMs = config.reminderHours * 60 * 60 * 1000;

    if (timeDiff <= reminderMs && timeDiff >= 0) {
      await sendTelegram({
        text: `⏰ Upcoming Meeting:\n\n` +
              `📅 ${meeting.summary || 'Untitled Meeting'}\n` +
              `⏰ ${formatTime(meeting.start?.dateTime || meeting.start?.date)} - ${formatTime(meeting.end?.dateTime || meeting.end?.date)}\n` +
              `📍 ${meeting.location || 'TBD'}\n` +
              `📅 ${formatDate(meeting.start?.dateTime || meeting.start?.date)}\n\n` +
              `${getAttendees(meeting.attendees)}`
      });

      checkedMeetings.add(meeting.id);
    }

    // Send notification for meetings today
    if (config.meetingsToCheck === 'all' || config.meetingsToCheck === 'today') {
      const meetingDate = new Date(meeting.start?.dateTime || meeting.start?.date).toDateString();
      const today = new Date().toDateString();

      if (meetingDate === today && timeDiff > 0) {
        await sendTelegram({
          text: `📅 Meeting Today:\n\n` +
                `⏰ ${formatTime(meeting.start?.dateTime || meeting.start?.date)} - ${formatTime(meeting.end?.dateTime || meeting.end?.date)}\n` +
                `📅 ${formatDate(meeting.start?.dateTime || meeting.start?.date)}\n` +
                `📍 ${meeting.location || 'TBD'}\n` +
                `📅 ${meeting.summary || 'Untitled Meeting'}`
        });

        checkedMeetings.add(meeting.id);
      }
    }
  }
}

/**
 * Send Telegram message via OpenClaw gateway
 */
async function sendTelegram(options) {
  try {
    const response = await fetch('http://localhost:3000/message', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'send',
        channel: 'telegram',
        target: config.telegramChatId,
        message: options.text
      })
    });

    if (!response.ok) {
      throw new Error('Failed to send Telegram message');
    }

    console.log(`Sent to Telegram: ${options.text.substring(0, 50)}...`);
    return await response.json();

  } catch (error) {
    console.error('Error sending Telegram:', error.message);
    throw error;
  }
}

/**
 * Format time (HH:MM)
 */
function formatTime(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
}

/**
 * Format date (Monday, Jan 1, 2026)
 */
function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
}

/**
 * Get attendees list
 */
function getAttendees(attendees) {
  if (!attendees || attendees.length === 0) return '';
  const emails = attendees
    .filter(a => a.email)
    .map(a => a.email.split('@')[0])
    .slice(0, 3);
  if (emails.length > 0) return `👥 ${emails.join(', ')}`;
  return '';
}

/**
 * Get meeting statistics
 */
function getStats() {
  return {
    lastChecked: new Date(lastChecked),
    checkedMeetingsCount: checkedMeetings.size,
    checkIntervalHours: config.checkInterval / 3600000,
    reminderHoursBefore: config.reminderHours,
    meetingsToCheck: config.meetingsToCheck,
    timezone: config.timezones,
    language: config.language,
    googleCalendarApiKeyConfigured: !!config.googleCalendarApiKey,
    calendarId: config.calendarId
  };
}

// CLI Interface
const args = process.argv.slice(2);

if (args[0] === 'check') {
  checkMeetings().then(count => {
    console.log(`Found ${count} meetings`);
    process.exit(0);
  });
} else if (args[0] === 'today') {
  checkMeetings().then(count => {
    console.log(`Found ${count} meetings for today`);
    process.exit(0);
  });
} else if (args[0] === 'status') {
  const stats = getStats();
  console.log('Meeting Manager Stats:');
  console.log(JSON.stringify(stats, null, 2));
} else if (args[0] === 'test') {
  // Test reminder (send next meeting)
  checkMeetings().then(count => {
    console.log('Test complete - check your Telegram');
    process.exit(0);
  });
} else {
  console.log('Usage:');
  console.log('  node scripts/meeting-manager.js check   - Check for all meetings');
  console.log('  node scripts/meeting-manager.js today   - Check meetings for today');
  console.log('  node scripts/meeting-manager.js status  - Show status');
  console.log('  node scripts/meeting-manager.js test    - Test reminder');
  process.exit(1);
}
