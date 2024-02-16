<template>
    <div class="modal fade" id="vaultModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Vault</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        Balance in Vault: <currency style="margin: 0;" /> {{ userStore.user.vault_balance.toBalance(2) }}
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text" style="border-right: 0; background-color: var(--bs-body-bg);"><currency style="margin: 0;"></currency></span>
                        <input id="vault-amount" style="border-left: 0; padding-left: 0;" type="number" step="0.01" class="form-control no-highlight" name="amount" placeholder="Enter Amount" required autofocus v-model="amount">
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary w-100" @click="deposit">
                                Deposit to Vault
                            </button>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary w-100" @click="withdraw">
                                Withdraw from Vault
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import * as bootstrap from 'bootstrap';
    import { ref } from 'vue';
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();

    let amount = ref(null);

    async function deposit() {
        let request = await axios.post("/api/vault/deposit", {
            amount: amount.value
        });

        if(!request.data.error) {
            userStore.updateUserBalance(request.data.data.balance);
            userStore.updateUserVaultBalance(request.data.data.vault_balance);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('vaultModal')).hide();
            amount.value = null;
        }
    }

    async function withdraw() {
        let request = await axios.post("/api/vault/withdraw", {
            amount: amount.value
        });

        if(!request.data.error) {
            userStore.updateUserBalance(request.data.data.balance);
            userStore.updateUserVaultBalance(request.data.data.vault_balance);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('vaultModal')).hide();
            amount.value = null;
        }
    }
</script>
