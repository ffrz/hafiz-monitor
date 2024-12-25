<script setup>
import BtnLink from "@/components/BtnLink.vue";
</script>
<template>
  <i-head title="Welcome" />
  <guest-layout>
    <q-page>
      <div class="row">
        <q-card class="col q-pa-xl q-ma-md q-mt-xl" square flat bordered>
          <div class="text-h4 text-center text-bold q-my-md">
            HoursHub Time Tracker App
          </div>
          <p class="text-subtitle1 text-center q-my-xl">
            HoursHub is an innovative internal time-tracking application
            designed to streamline employee work-hour management and project
            tracking. With a user-friendly interface and robust functionality,
            HoursHub helps businesses accurately monitor productivity and
            simplify time management processes.
          </p>
          <p class="text-center q-my-sm">
            <btn-link
              color="accent"
              label="Click Here to Login"
              :href="route('admin.auth.login')"
            />
          </p>
          <p class="text-subtitle1 q-mt-xl">Key Features:</p>
          <ul>
            <li>
              Intuitive Time Tracking: Easily log work hours with a built-in
              timer or manual input options.
            </li>
            <li>
              Project Management Integration: Assign tasks and track progress
              across multiple projects.
            </li>
            <li>
              User Management: Configure roles and permissions to ensure secure
              and efficient collaboration.
            </li>
            <li>
              Client Management: Manage client information and associate
              projects for seamless invoicing and reporting.
            </li>
            <li>
              Real-Time Analytics: Gain insights into productivity through
              detailed reports and visual dashboards.
            </li>
            <li>
              Mobile Responsiveness: Access and manage time tracking on-the-go
              with a responsive design.
            </li>
          </ul>
          <p class="text-subtitle1">
            Whether you’re a startup or a growing company, HoursHub ensures
            accurate, transparent, and hassle-free time tracking for better
            workforce efficiency.
          </p>
        </q-card>
      </div>
    </q-page>
  </guest-layout>
</template>
