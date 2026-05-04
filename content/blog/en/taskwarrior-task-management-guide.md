---
Title: "Taskwarrior guide: efficient task management (and Taskserver sync)"
Description: Use Taskwarrior on one machine, then sync tasks across devices with Taskserver—installation and basics.
Author: Luis Amigo
Date: 2023-09-01 01:05PM
Template: post
Tags: Productividad, Sistemas
Lang: en
Translation_Key: praderas-b5-taskwarrior-guide
---

Taskwarrior is a **CLI task manager** that stays fast even when your backlog grows. This guide covers single-machine usage and a **Taskserver** path for multi-device sync. Commands reflect common Linux distributions—adapt package names for your OS.

## Part 1: Taskwarrior on one machine

### What is Taskwarrior?

A command-line inbox for actionable work: create, tag, schedule, prioritize, and complete tasks without leaving the terminal.

### Install

**Debian / Ubuntu:**

```bash
sudo apt-get update
sudo apt-get install taskwarrior
```

**RHEL family:**

```bash
sudo yum install task
```

### Basic commands

1. **Add a task**

```bash
task add Finish project report
```

2. **List pending work**

```bash
task list
```

3. **Complete by ID**

```bash
task 1 done
```

4. **Filter by tag**

```bash
task +important list
```

## Part 2: Multiple machines with Taskserver

### What is Taskserver?

The companion **sync service** that lets several Taskwarrior clients share one queue—handy when you bounce between laptop and desktop.

### Build / install Taskserver (outline)

1. **Clone sources**

```bash
git clone https://github.com/GothenburgBitFactory/taskserver.git
```

2. **Dependencies on Debian/Ubuntu**

```bash
sudo apt-get install cmake make g++ uuid-dev libgnutls28-dev libssl-dev
```

3. **Compile**

```bash
cd taskserver
cmake .
make
sudo make install
```

### Configure Taskserver (minimal)

Create a data directory, copy the sample `taskserver.rc`, and set `server` plus `data.location` to match your environment—then start the daemon using the packaging or unit files your distro provides.

### Point Taskwarrior at the server

```bash
task config taskd.server <your_taskserver_host>
task config taskd.credentials <user>@<your_taskserver_host>
task sync init
```

Repeat client configuration on each machine you want on the same queue.

## Conclusion

Taskwarrior rewards **plain-text discipline**: small habits (tags, due dates, weekly reviews) compound. Taskserver adds **multi-device continuity** once you accept the operational cost of hosting sync yourself.

For advanced workflows—UDAs, hooks, burndown reports—lean on upstream docs; the defaults here are enough to ship value quickly.
