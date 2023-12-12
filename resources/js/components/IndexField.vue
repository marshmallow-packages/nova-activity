<template>
    <div>
        <LinkButton @click="show = true">
            {{ __('novaActivity.open_index_model') }}
        </LinkButton>
        <Modal
            :show="show"
            @close-via-escape="closeModel"
            role="dialog"
            size="2xl"
            modal-style="window"
        >
            <div
                class="space-y-6 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800"
            >
                <div class="space-y-6">
                    <ModalHeader>
                        {{ field.name }}
                    </ModalHeader>
                    <div class="tw-p-6">
                        <NovaActivityList
                            :resourceName="resourceName"
                            :field="field"
                            :resourceId="resource.id.value"
                        />
                    </div>
                </div>

                <ModalFooter>
                    <div class="flex items-center ml-auto">
                        <CancelButton
                            component="button"
                            type="button"
                            dusk="cancel-action-button"
                            class="ml-auto mr-3"
                            @click="closeModel"
                        >
                            {{ __('novaActivity.close_index_model') }}
                        </CancelButton>
                    </div>
                </ModalFooter>
            </div>
        </Modal>
    </div>
</template>

<script>
    import NovaActivityList from "./NovaActivityList";

    export default {
        props: ["index", "resource", "resourceName", "resourceId", "field"],
        components: { NovaActivityList },
        data() {
            return {
                show: false,
            };
        },
        created: function () {
            console.log(this.field.name);
        },
        methods: {
            closeModel() {
                this.show = false;
            },
        },
    };
</script>
