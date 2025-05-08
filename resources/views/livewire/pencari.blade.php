<x-app-layout>
    <div class="col d-flex justify-content-center">


        <div class="card shadow-black align-center mt-4 border-secondary text-white w-75 ">
            <div class="card-header bg-secondary">
                <b>Formulir Sample Kopi (Pencari)</b>
            </div>
            <div class="card-body">
                <form method="POST" action="/submit">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="fragrance" :value="__('Fragrance')" />
                            <small class="text-muted d-flex align-items-stretch">
                                <h5>Intensity</h5>
                            </small>
                        </div>
                        <div class="col-md-9">
                            <x-level-input name="fragrance" id="fragrance" :numberRange="[1, 15]" :labels="['Low', 'Medium', 'High']" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="Aroma" :value="__('Aroma')" />
                            <small class="text-muted">
                                <h5>Intensity</h5>
                            </small>
                        </div>

                        <div class="col-md-9">
                            <x-level-input name="aroma_intensity" id="AromaIntensity" :numberRange="[1, 15]"
                                :labels="['Low', 'Medium', 'High']" />
                        </div>
                    </div>

                    <div class="d-flex pt-3 justify-content-end">
                        <div class="col-md-9 d-flex flex-row">
                            <!-- Kolom pertama -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="enzymatic" name="aroma_check"
                                    label="Enzymatic (Flowery, Fruity, Herby)" />
                                <x-checkbox id="sugar_browning" name="aroma_check"
                                    label="Sugar Browning (Nutty, Caramelly, Chocolately)" />
                            </div>
                            <!-- Kolom kedua -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="dry_distillation" name="aroma_check"
                                    label="Dry Distillation (Carbony, Spicy, Resinous)" />
                            </div>
                        </div>
                    </div>




                    <div class="row pt-4">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="Acidity" :value="__('Acidity')" />
                            <small class="text-muted d-flex align-items-stretch">
                                <h5>Intensity</h5>
                            </small>
                        </div>
                        <div class="col-md-9">
                            <x-level-input name="acidity" id="acidity" :numberRange="[1, 15]" :labels="['Low', 'Medium', 'High']" />
                        </div>
                    </div>

                    <div class="d-flex pt-3 justify-content-end">
                        <div class="col-md-9 d-flex flex-row">
                            <!-- Kolom pertama -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="citrc_acid" name="acidity_check"
                                    label="Citric Acid (Citrus, Tropical)" />
                                <x-checkbox id="malic_acid" name="acidity_check"
                                    label="Malic Acid (Apple, Berry, Stone Fruit)" />
                                <x-checkbox id="lactic_acid" name="acidity_check"
                                    label="Lactic Acid (Yogurt, Cheese)" />
                                <x-checkbox id="acetic_acid" name="acidity_check"
                                    label="Acetic Acid (Winey, Fermenty, Vinegary)" />
                            </div>
                            <!-- Kolom kedua -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="tartaric_acid" name="acidity_check"
                                    label="Tartaric Acid (Grape-Like)" />
                                <x-checkbox id="phosporic_acid" name="acidity_check"
                                    label="Phosporic Acid (Soft Drink-Like)" />
                                <x-checkbox id="quinic_acid" name="acidity_check" label="Quiniq Acid (Bitter)" />
                            </div>
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="Mouthfeel" :value="__('Mouthfeel')" />
                            <small class="text-muted d-flex align-items-stretch">
                                <h5>Texture</h5>
                            </small>
                        </div>
                        <div class="col-md-9">
                            <x-level-input name="mouthfeel" id="mouthfeel" :numberRange="[1, 15]" :labels="['Smooth', 'Particles', 'Rough']" />
                        </div>
                    </div>

                    <div class="d-flex pt-3 justify-content-end">
                        <div class="col-md-9 d-flex flex-row">
                            <!-- Kolom pertama -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="smooth" name="mouthfeel_check"
                                    label="Smooth (Creamy, Round, Syrupy, Oily, Silky)" />
                                <x-checkbox id="malic_acid" name="mouthfeel_check"
                                    label="Particles (Powdery, Grainy, Gritty)" />

                            </div>
                            <!-- Kolom kedua -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="rough" name="mouthfeel_check" label="Rough (Coarse, Harsh, Hard)" />

                            </div>
                        </div>
                    </div>

                    <div class="row pt-4">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="Mouthfeel Body" :value="__('Mouthfeel Body')" />
                            <small class="text-muted d-flex align-items-stretch">
                                <h5>body</h5>
                            </small>
                        </div>
                        <div class="col-md-9">
                            <x-level-input name="mouthfeel_body" id="mouthfeel_body" :numberRange="[1, 15]"
                                :labels="['Light/Ringan', 'Medium/Sedang', 'Heavy/Berat', 'Full/Penuh']" />
                        </div>
                    </div>

                    <div class="d-flex pt-3 justify-content-end">
                        <div class="col-md-9 d-flex flex-row">
                            <!-- Kolom pertama -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="watery" name="mouthfeel_body_check" label="Watery/Encer" />
                                <x-checkbox id="thin" name="mouthfeel_body_check" label="Thin/Tipis" />
                                <x-checkbox id="juicy" name="mouthfeel_body_check" label="Juicy" />
                            </div>
                            <!-- Kolom kedua -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="thick" name="mouthfeel_body_check" label="Thick/Tebal" />
                                <x-checkbox id="sticky" name="mouthfeel_body_check" label="Sticky/Lengket" />
                                <x-checkbox id="coating" name="mouthfeel_body_check" label="Coating/Berlapis" />

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="After Taste" :value="__('After Taste')" />
                            <small class="text-muted d-flex align-items-stretch">
                                <h5>body</h5>
                            </small>
                        </div>
                        <div class="col-md-9">
                            <x-level-input name="aftertaste" id="aftertaste" :numberRange="[1, 15]" :labels="['Short', 'Medium', 'Long']" />
                        </div>
                    </div>


                    <div class="row pt-4">
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-start">
                            <x-input-label for="Overall" :value="__('Overall')" />
                        </div>

                        <div class="col-md-9 d-flex flex-row">
                            <!-- Kolom pertama -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="balance" name="overall_check" label="Balanced" />
                                <x-checkbox id="unbalanced" name="overall_check" label="Unbalanced" />
                                <x-checkbox id="clean" name="overall_check" label="Clean" />
                                <x-checkbox id="dirty" name="overall_check" label="Dirty" />
                            </div>
                            <!-- Kolom kedua -->
                            <div class="col-md-6 d-flex flex-column  align-items-start">
                                <x-checkbox id="poor_complexity" name="overall_check" label="Poor Complexity" />
                                <x-checkbox id="high_complexity" name="overall_check" label="High Complexity" />
                                <x-checkbox id="dry" name="overall_check" label="Dry" />
                                <x-checkbox id="astringent" name="overall_check" label="Astringent" />

                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Submit</button>
                    <button type="reset" class="mt-4 bg-red-700 text-white px-4 py-2 rounded-md">Reset</button>
                </form>
            </div>
        </div>
    </div>


</x-app-layout>
