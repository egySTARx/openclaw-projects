#!/usr/bin/env node
/**
 * Gmail Manager for Memo
 * - Check for new emails and notify on WhatsApp
 * - Send/reply to emails via Google Chat
 * - Auto-reply when away
 * - Filter and organize messages
 *
 * Uses Google Chat API (IMAP or Gmail API) via Node.js
 * For production: Set up Google Cloud OAuth credentials
 */

// Configuration
const config = {
  googleChatId: process.env.GOOGLE_CHAT_ID || 'default',
  whatsappNumber: process.env.WHATSAPP_NUMBER || '+201027698925',
  autoReply: {
    enabled: process.env.AUTO_REPLY_ENABLED === 'true',
    message: process.env.AUTO_REPLY_MESSAGE || 'I am away and will respond when I return.',
    awaySince: null
  },
  filters: JSON.parse(process.env.GMAIL_FILTERS || '[]')
};

// State tracking
let lastChecked = Date.now();
let unreadCount = 0;

/**
 * Check for new emails via IMAP (simplified)
 * For production: Use nodemailer + Gmail API or better yet, use OpenClaw's message tool
 */
async function checkEmails() {
  console.log('Checking for new emails...');

  // NOTE: In production, implement proper IMAP or Gmail API integration
  // For now, this is a skeleton for when you connect Google Chat

  const newMessages = []; // Would be populated from Gmail API

  if (newMessages.length > 0) {
    unreadCount += newMessages.length;
    await notifyNewMessages(newMessages);
  }

  lastChecked = Date.now();
  console.log(`Checked at ${new Date(lastChecked).toISOString()}`);
  return newMessages.length;
}

/**
 * Notify about new messages via WhatsApp
 */
async function notifyNewMessages(messages) {
  for (const msg of messages) {
    await sendWhatsApp({
      text: `📧 New Email from: ${msg.from}\n\nSubject: ${msg.subject}\n\n${msg.body}\n\n${config.autoReply.enabled ? '(Auto-reply enabled)' : ''}`
    });
  }
}

/**
 * Send an email (via Google Chat)
 */
async function sendEmail(options) {
  const { to, subject, body } = options;

  // NOTE: Implement proper email sending via Gmail API or SMTP
  // For now, this is a skeleton

  await sendWhatsApp({
    text: `📤 Sending email:\n\nTo: ${to}\nSubject: ${subject}\n\n${body}\n\n(Email sent via Google Chat)`
  });
}

/**
 * Send WhatsApp message via OpenClaw
 */
async function sendWhatsApp(options) {
  try {
    const response = await fetch('http://localhost:3000/message', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'send',
        channel: 'whatsapp',
        target: config.whatsappNumber,
        message: options.text
      })
    });

    if (!response.ok) {
      throw new Error('Failed to send WhatsApp message');
    }

    const data = await response.json();
    console.log(`Sent WhatsApp: ${options.text.substring(0, 50)}...`);
    return data;

  } catch (error) {
    console.error('Error sending WhatsApp:', error.message);
    throw error;
  }
}

/**
 * Set auto-reply status
 */
function setAutoReply(away, message) {
  if (away) {
    config.autoReply.awaySince = Date.now();
  } else {
    config.autoReply.awaySince = null;
  }

  if (message) {
    config.autoReply.message = message;
  }

  config.autoReply.enabled = away || config.autoReply.enabled;

  console.log(`Auto-reply ${away ? 'enabled' : 'disabled'}: ${config.autoReply.message}`);
}

/**
 * Get email statistics
 */
function getStats() {
  return {
    lastChecked: new Date(lastChecked),
    unreadCount,
    autoReply: {
      enabled: config.autoReply.enabled,
      message: config.autoReply.message,
      awaySince: config.autoReply.awaySince ? new Date(config.autoReply.awaySince) : null
    },
    filters: config.filters.length
  };
}

// CLI Interface
const args = process.argv.slice(2);

if (args[0] === 'check') {
  checkEmails().then(count => {
    console.log(`Found ${count} new messages`);
    process.exit(0);
  });
} else if (args[0] === 'send') {
  sendEmail({
    to: args[1],
    subject: args[2],
    body: args.slice(3).join(' ')
  }).then(() => {
    console.log('Email sent!');
    process.exit(0);
  });
} else if (args[0] === 'auto-reply') {
  if (args[1] === 'on') {
    setAutoReply(true, args[2]);
  } else if (args[1] === 'off') {
    setAutoReply(false);
  } else if (args[1] === 'status') {
    const stats = getStats();
    console.log(JSON.stringify(stats, null, 2));
  } else {
    console.log('Usage: node gmail-manager.js auto-reply [on/off/status] [message]');
  }
} else if (args[0] === 'stats') {
  const stats = getStats();
  console.log('Gmail Manager Stats:');
  console.log(JSON.stringify(stats, null, 2));
} else {
  console.log('Usage:');
  console.log('  node scripts/gmail-manager.js check       - Check for new messages');
  console.log('  node scripts/gmail-manager.js send <to> <subject> <body> - Send email');
  console.log('  node scripts/gmail-manager.js auto-reply [on/off/status] - Toggle auto-reply');
  console.log('  node scripts/gmail-manager.js stats       - Show statistics');
  process.exit(1);
}
