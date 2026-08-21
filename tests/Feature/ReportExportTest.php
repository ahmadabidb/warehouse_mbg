<?php

it('has dedicated pdf templates for reports', function () {
    expect(view()->exists('reports.pdf.transaction'))->toBeTrue();
    expect(view()->exists('reports.pdf.opname'))->toBeTrue();
});
