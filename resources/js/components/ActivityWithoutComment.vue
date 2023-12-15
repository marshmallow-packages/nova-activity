<template>
    <li v-if="!hidden" class="tw-relative tw-flex tw-gap-x-4">
        <div
            class="tw-absolute tw-left-0 tw-top-0 tw-flex tw-w-6 tw-justify-center tw--bottom-6"
        >
            <div class="tw-w-px tw-bg-gray-200 dark:tw-bg-gray-700"></div>
        </div>
        <div
            class="tw-relative tw-flex tw-h-6 tw-w-6 tw-flex-none tw-items-center tw-justify-center"
        >
            <div
                class="tw-h-1.5 tw-w-1.5 tw-rounded-full tw-bg-gray-100 tw-ring-1 tw-ring-gray-300"
            ></div>
        </div>
        <div
            class="tw-flex-auto tw-py-0.5 tw-text-xs tw-leading-5 dark:text-gray-400 tw-text-gray-500"
        >
            <div
                class="tw-font-medium dark:tw-text-gray-400 tw-text-gray-900 tw-flex"
            >
                <ActivityStateIcons :comment="comment" />
                <div>
                    {{ comment.user.name }}
                    <span class="tw-text-gray-500 dark:text-gray-400">{{
                        comment.type.label
                    }}</span>
                </div>
            </div>
        </div>
        <time
            class="tw-flex-none tw-py-0.5 tw-text-xs tw-leading-5 tw-text-gray-500"
        >
            <span v-if="field.use_human_readable_dates">
                {{ comment.time_ago }}
            </span>
            <span v-if="!field.use_human_readable_dates">
                {{ comment.created_at }}
            </span>
        </time>
        <ActiviyActions :comment="comment" />
    </li>
</template>

<script>
    import ActiviyActions from "./ActiviyActions";
    import ActivityStateIcons from "./ActivityStateIcons";
    export default {
        props: ["resourceName", "resourceId", "field", "comment", "hidden"],

        components: { ActiviyActions, ActivityStateIcons },
    };
</script>
