<?php

namespace Lasallesoftware\Novabackend\Nova\Fields;


// LaSalle Software class
use Lasallesoftware\Novabackend\Nova\Fields\BaseField;

// Other classes
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use DateTimeInterface;
use Exception;
use Illuminate\Support\Arr;
use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Fields\Filters\DateFilter;
use Laravel\Nova\Http\Requests\NovaRequest;


class CustomDate extends BaseField implements FilterableField
{
    use \Laravel\Nova\Fields\FieldFilterable;
    use \Laravel\Nova\Fields\SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-field';

    /**
     * The minimum value that can be assigned to the field.
     *
     * @var string|null
     */
    public $min = null;

    /**
     * The maximum value that can be assigned to the field.
     *
     * @var string|null
     */
    public $max = null;

    /**
     * The step size the field will increment and decrement by.
     *
     * @var string|int|null
     */
    public $step = null;

    /**
     * Create a new field.
     *
     * @param  \Stringable|string  $name
     * @param  string|callable|object|null  $attribute
     * @param  (callable(mixed, mixed, ?string):(mixed))|null  $resolveCallback
     * @return void
     */
    public function __construct($name, mixed $attribute = null, ?callable $resolveCallback = null)
    {
       parent::__construct($name, $attribute, $resolveCallback ?? function ($value) {

            //if (isset($value)) {
            if (! is_null($value)) {
                if (! $value instanceof DateTimeInterface) {
                    throw new Exception(__('lasallesoftwarelibrarybackend::general.exception_message_date_cast'));
                }
            }
        });

        $this->formatTheValueForTheFormWeAreOn($this->identifyForm());

        $this->specifyShowOnForms();

        $this->withMeta(['placeholder' => $attribute ?? __('lasallesoftwarelibrarybackend::general.not_specified')]);
    }


    /* =============================================================================
       START: MY OWN METHODS
       ============================================================================= */
    /**
     * Set the first day of the week.
     *
     * @param  int  $day
     * @return $this
     */
    public function firstDayOfWeek($day)
    {
        return $this->withMeta([__FUNCTION__ => $day]);
    }

    /**
     * Set the date format (Moment.js) that should be used to display the date.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        return $this->withMeta([__FUNCTION__ => $format]);
    }

    /**
     * This field will display, or not display, on these forms.
     *
     * @return $this
     */
    private function specifyShowOnForms()
    {
        $this->showOnIndex    = false;
        $this->showOnDetail   = true;
        $this->showOnCreation = true;
        $this->showOnUpdate   = true;

        return $this;
    }

    /**
     * Format this field for the individual forms,
     *
     * @param string  $formType  The form being displayed.
     *                           From Lasallesoftware\Novabackend\Nova\Fields->identifyForm()
     * @return \Closure
     */
    private function formatTheValueForTheFormWeAreOn($formType)
    {
        // BECAUSE I CANNOT NOW FORMAT THE DATE, AND I DO NOT GIVE A SHIT ABOUT FORMATTING THE DATE,
        // IT IS JUST THIS ONE LITTLE IF STATEMENT
        // I AM RETAINING THE FORMAT FOR REFERENCE ONLY
        return $this->resolveCallback = function ($value) {
            if (! is_null($value)) {
                return $value;
            }
            //return $value->format('l F dS, Y');
        };  
    }
    /* =============================================================================
       END: MY OWN METHODS
       ============================================================================= */


    /**
     * The minimum value that can be assigned to the field.
     *
     * @return $this
     */
    public function min(CarbonInterface|string $min)
    {
        if (is_string($min)) {
            $min = Carbon::parse($min);
        }

        $this->min = $min->toDateString();

        return $this;
    }

    /**
     * The maximum value that can be assigned to the field.
     *
     * @return $this
     */
    public function max(CarbonInterface|string $max)
    {
        if (is_string($max)) {
            $max = Carbon::parse($max);
        }

        $this->max = $max->toDateString();

        return $this;
    }

    /**
     * The step size the field will increment and decrement by.
     *
     * @return $this
     */
    public function step(CarbonInterval|string|int $step)
    {
        $this->step = $step instanceof CarbonInterval ? $step->totalDays : $step;

        return $this;
    }

    /**
     * Resolve the default value for the field.
     *
     * @return \Laravel\Nova\Support\UndefinedValue|string|null
     */
    #[\Override]
    public function resolveDefaultValue(NovaRequest $request): mixed
    {
        /** @var \Laravel\Nova\Support\UndefinedValue|\DateTimeInterface|string|null $value */
        $value = parent::resolveDefaultValue($request);

        if ($value instanceof DateTimeInterface) {
            return $value instanceof CarbonInterface
                        ? $value->toDateString()
                        : $value->format('Y-m-d');
        }

        return $value;
    }

    /**
     * Make the field filter.
     *
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new DateFilter($this);
    }

    /**
     * Define the default filterable callback.
     *
     * @return callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Contracts\Database\Eloquent\Builder, mixed, string):\Illuminate\Contracts\Database\Eloquent\Builder
     */
    protected function defaultFilterableCallback()
    {
        return function (NovaRequest $request, $query, $value, $attribute) {
            [$min, $max] = $value;

            if (! is_null($min) && ! is_null($max)) {
                return $query->whereBetween($attribute, [$min, $max]);
            } elseif (! is_null($min)) {
                return $query->where($attribute, '>=', $min);
            }

            return $query->where($attribute, '<=', $max);
        };
    }

    /**
     * Prepare the field for JSON serialization.
     */
    public function serializeForFilter(): array
    {
        return transform($this->jsonSerialize(), function ($field) {
            return Arr::only($field, [
                'uniqueKey',
                'name',
                'attribute',
                'type',
                'placeholder',
                'extraAttributes',
            ]);
        });
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), array_filter([
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step ?? 'any',
        ]));
    }
}
